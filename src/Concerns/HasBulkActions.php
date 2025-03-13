<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Contracts\ShouldChunk;
use Illuminate\Database\Eloquent\Collection as DatabaseCollection;
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
    public function chunk($chunk = true)
    {
        $this->chunk = $chunk;

        return $this;
    }

    /**
     * Determine if the action should be chunked.
     *
     * @return bool
     */
    public function isChunked()
    {
        if ($this instanceof ShouldChunk) {
            return true;
        }

        return (bool) ($this->chunk ?? static::fallbackChunked());
    }

    /**
     * Determine if the action should be chunked from the config.
     *
     * @return bool
     */
    public static function fallbackChunked()
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
     *
     * @return bool
     */
    public function chunksById()
    {
        return (bool) ($this->chunkById ?? static::fallbackChunksById());
    }

    /**
     * Determine if the action should chunk the records by id from the config.
     *
     * @return bool
     */
    public static function fallbackChunksById()
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
        return $this->chunkSize ?? static::fallbackChunkSize();
    }

    /**
     * Get the size of the chunk to use when chunking the records from the config.
     *
     * @return int
     */
    public static function fallbackChunkSize()
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
            return null;
        }

        $model = $builder->getModel()::class;
        $type = $this->getHandlerType($handler, $model);

        if ($type === 'builder' && $this->isChunked()) {
            static::throwInvalidHandlerReference();
        }

        $handler = $type === 'model'
            ? fn ($records) => $records->each($handler)
            : $handler;

        if ($this->isChunked()) {
            $handler = $this->wrapHandlerForChunking($handler);
        } elseif ($type !== 'builder') {
            $builder = $builder->get();
        }

        [$named, $typed] = $this->getEvaluationParameters($model, $builder);

        return $this->evaluate($handler, $named, $typed);
    }

    /**
     * Wrap the handler for chunking.
     *
     * @param  \Closure  $handler
     * @return \Closure
     */
    protected function wrapHandlerForChunking($handler)
    {
        $chunkSize = $this->getChunkSize();

        return $this->chunksById()
            ? fn ($builder) => $builder->chunkById($chunkSize, $handler)
            : fn ($builder) => $builder->chunk($chunkSize, $handler);
    }

    /**
     * Determine if the handler type references the builder, collection, or model.
     *
     * @param  \Closure  $handler
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @return 'builder'|'collection'|'model'|null
     */
    public function getHandlerType($handler, $model)
    {
        $parameters = (new \ReflectionFunction($handler))->getParameters();

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();

            $typed = $parameter->getType();

            $type = $typed instanceof \ReflectionNamedType
                ? $typed->getName()
                : null;

            if (static::isBuilder($name, $type)) {
                return 'builder';
            }

            if (static::isCollection($name, $type)) {
                return 'collection';
            }

            if (static::isModel($name, $type, $model)) {
                return 'model';
            }
        }

        return null;
    }

    /**
     * Get the named and typed parameters to use for the bulk action.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Collection<int,\Illuminate\Database\Eloquent\Model>  $value
     * @return array{array<string, mixed>,  array<class-string, mixed>}
     */
    protected function getEvaluationParameters($model, $value)
    {
        [$named, $typed] = static::getBuilderParameters($model, $value);

        $named = \array_merge($named, [
            'collection' => $value,
        ]);

        $typed = \array_merge($typed, [
            DatabaseCollection::class => $value,
            Collection::class => $value,
        ]);

        return [$named, $typed];
    }

    /**
     * Throw an exception if the handler references the builder.
     *
     * @return never
     *
     * @throws \RuntimeException
     */
    protected static function throwInvalidHandlerReference()
    {
        throw new \RuntimeException(
            'A chunked handler cannot reference the builder.'
        );
    }
}
