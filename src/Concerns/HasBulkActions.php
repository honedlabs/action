<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Illuminate\Support\Collection;
use Honed\Action\Contracts\ShouldChunk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as DatabaseCollection;

trait HasBulkActions
{
    use HasAction;

    /**
     * Whether the action should be chunked.
     *
     * @var bool|null
     */
    protected $chunk;

    /**
     * Whether the bulk action should chunk the records by id.
     *
     * @var bool|null
     */
    protected $chunkById;

    /**
     * The size of the chunk to use when chunking the records.
     *
     * @var int|null
     */
    protected $chunkSize;

    /**
     * Set the action to chunk the records.
     *
     * @param  bool|null  $chunk
     * @return $this
     */
    public function chunk($chunk = true): static
    {
        $this->chunk = $chunk;

        return $this;
    }

    /**
     * Determine if the action should be chunked.
     *
     * @return bool
     */
    public function isChunked(): bool
    {
        if ($this instanceof ShouldChunk) {
            return true;
        }

        if (isset($this->chunk)) {
            return $this->chunk;
        }

        return $this->fallbackChunked();
    }

    /**
     * Determine if the action should be chunked from the config.
     *
     * @return bool
     */
    public function fallbackChunked(): bool
    {
        return (bool) config('action.chunk', false);
    }

    /**
     * Set the action to chunk the records by id.
     *
     * @param  bool|null  $chunkById
     * @return $this
     */
    public function chunkById($chunkById = true)
    {
        $this->chunkById = $chunkById;

        return $this;
    }

    /**
     * Determine if the action should chunk the records by id.
     *
     * @return bool
     */
    public function chunksById(): bool
    {
        return (bool) ($this->chunkById ?? $this->fallbackChunksById());
    }

    /**
     * Determine if the action should chunk the records by id from the config.
     *
     * @return bool
     */
    public function fallbackChunksById(): bool
    {
        return (bool) config('action.chunk_by_id', true);
    }

    /**
     * Set the size of the chunk to use when chunking the records.
     *
     * @param  int|null  $size
     * @return $this
     */
    public function chunkSize($size)
    {
        $this->chunkSize = $size;

        return $this;
    }

    /**
     * Get the size of the chunk to use when chunking the records.
     *
     * @return int
     */
    public function getChunkSize()
    {
        return $this->chunkSize ?? $this->fallbackChunkSize();
    }

    /**
     * Get the size of the chunk to use when chunking the records from the config.
     *
     * @return int
     */
    public function fallbackChunkSize(): int
    {
        return type(config('action.chunk_size', 1000))->asInt();
    }

    /**
     * Execute the bulk action on the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     * @return mixed
     */
    public function execute($builder)
    {
        $handler = $this->getHandler();

        if (! $handler) {
            return;
        }

        $references = $this->hasReferenceTo($handler);
        
        if ($this->isChunked()) {
            $handler = $this->chunkHandler($handler, $references);
        } elseif ($references === 'model') {
            $builder = $builder->get();
        }

        [$named, $typed] = $this->getEvaluationParameters($builder);

        return $this->evaluate($handler, $named, $typed);
    }

    /**
     * Get the handler using chunking.
     *
     * @param  callable  $handler
     * @param  'builder'|'collection'|'model'  $references
     * @return \Closure
     */
    public function chunkHandler($handler, $references)
    {
        $chunkSize = $this->getChunkSize();

        // If the handler given contains references to the builder, we throw an
        // error as they cannot access the builder in the chunked context. We only
        // need to check one variable, and default it to the model.
        if ($references === 'builder') {
            throw new \RuntimeException(
                'A chunked handler cannot reference the builder.'
            );
        }

        // If the handler references the collection, we can use the collection's
        // each method to iterate over the records. Otherwise, we need to wrap
        // the handler in a closure that will iterate over the records.
        $handler = $references === 'collection'
            ? $handler
            : fn (Collection $records) => $records->each($handler);

        if ($this->chunksById()) {
            return fn ($builder) => $builder
                ->chunkById($chunkSize, $handler);
        }

        return fn ($builder) => $builder
            ->chunk($chunkSize, $handler);
    }

    /**
     * Determine if the chunked handler references the builder, collection, or model.
     *
     * @param  \Closure  $handler
     * @return 'builder'|'collection'|'model'
     */
    public function hasReferenceTo($handler)
    {
        $parameters = (new \ReflectionFunction($handler))->getParameters();

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType()?->getName();

            if (\in_array($name, ['builder', 'query']) ||
                $type === Builder::class
            ) {
                return 'builder';
            }

            if (\in_array($name, ['collection', 'records']) ||
                \in_array($type, [DatabaseCollection::class, Collection::class])
            ) {
                return 'collection';
            }
        }

        return 'model';
    }

    /**
     * Get the named and typed parameters to use for callable evaluation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $records
     * @return array{array<string, mixed>,  array<class-string, mixed>}
     */
    protected function getEvaluationParameters($records)
    {
        [$model, $singular, $plural] = $this->getParameterNames($records);

        $named = \array_fill_keys([
            'model',
            'record',
            'builder',
            'query',
            'records',
            'collection',
            $singular,
            $plural,
        ], $records);

        $typed = \array_fill_keys([
            Builder::class,
            DatabaseCollection::class,
            Collection::class,
            Model::class,
            $model::class,
        ], $records);

        return [$named, $typed];
    }
}
