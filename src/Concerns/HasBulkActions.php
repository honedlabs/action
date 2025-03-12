<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Contracts\ShouldChunk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as DatabaseCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
        $this->chunk = true;
        $this->chunkById = $chunkById;

        return $this;
    }

    /**
     * Determine if the action should chunk the records by id.
     */
    public function chunksById(): bool
    {
        return (bool) ($this->chunkById ?? $this->fallbackChunksById());
    }

    /**
     * Determine if the action should chunk the records by id from the config.
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
     *
     * @throws \RuntimeException
     */
    public function execute($builder)
    {
        $handler = $this->getHandler();

        if (! $handler) {
            return;
        }

        $model = $builder->getModel();
        $reference = $this->references($handler, $model);

        // First handle the builder case which might throw an exception
        if ($reference === 'builder' && $this->isChunked()) {
            throw new \RuntimeException('A chunked handler cannot reference the builder.');
        }

        // Then use a single match for the remaining logic
        [$builder, $handler] = match ($reference) {
            'model' => [
                ! $this->isChunked() ? $builder->get() : $builder,
                fn ($records) => $records->each($handler),
            ],

            'collection' => [
                ! $this->isChunked() ? $builder->get() : $builder,
                $handler,
            ],

            default => [$builder, $handler]
        };

        // Apply chunking if needed
        if ($this->isChunked()) {
            $handler = $this->chunksById()
                ? fn ($builder) => $builder->chunkById($this->getChunkSize(), $handler)
                : fn ($builder) => $builder->chunk($this->getChunkSize(), $handler);
        }

        [$named, $typed] = $this->getEvaluationParameters($model::class, $builder);

        return $this->evaluate($handler, $named, $typed);
    }

    /**
     * Determine if the chunked handler references the builder, collection, or model.
     *
     * @param  \Closure  $handler
     * @param  class-string<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model  $model
     * @return 'builder'|'collection'|'model'|null
     */
    public function references($handler, $model)
    {
        [$model, $singular, $plural] = $this->getParameterNames($model);
        $parameters = (new \ReflectionFunction($handler))->getParameters();

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $reflected = $parameter->getType();
            $type = $reflected instanceof \ReflectionNamedType
                ? $reflected->getName()
                : null;

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

            if (\in_array($name, [$singular, $plural, 'record', 'model']) ||
                \in_array($type, [Model::class, $model])
            ) {
                return 'model';
            }
        }

        return null;
    }

    /**
     * Get the named and typed parameters to use for callable evaluation.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $model
     * @param  mixed  $value
     * @return array{array<string, mixed>,  array<class-string, mixed>}
     */
    protected function getEvaluationParameters($model, $value)
    {
        [$model, $singular, $plural] = $this->getParameterNames($model);

        $named = \array_fill_keys(
            ['model', 'record', 'builder', 'query', 'records', 'collection', $singular, $plural],
            $value
        );

        $typed = \array_fill_keys(
            [Builder::class, DatabaseCollection::class, Collection::class, Model::class, $model],
            $value
        );

        return [$named, $typed];
    }
}
