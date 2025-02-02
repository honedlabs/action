<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Contracts\ShouldChunk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

trait ChunksBuilder
{
    /**
     * @var bool|null
     */
    protected $chunk;

    /**
     * @var int|null
     */
    protected $chunkSize;

    /**
     * @var bool|null
     */
    protected $chunkById;

    /**
     * Set the action to chunk the records.
     *
     * @return $this
     */
    public function chunk(int $size = 1000, bool $chunkById = true): static
    {
        $this->chunk = true;
        $this->chunkSize = $size;
        $this->chunkById = $chunkById;

        return $this;
    }

    /**
     * Determine if the action should chunk the records.
     */
    public function chunks(): bool
    {
        return $this instanceof ShouldChunk || (bool) $this->chunk;
    }

    /**
     * Get the chunk size.
     */
    public function getChunkSize(): int
    {
        return $this->chunkSize ?? 1000;
    }

    /**
     * Determine if the action should chunk by id.
     */
    public function chunksById(): bool
    {
        return $this->chunkById ?? true;
    }

    /**
     * Chunk the records using the builder.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  \Illuminate\Database\Eloquent\Builder<T>  $builder
     */
    public function chunkRecords(Builder $builder, callable $callback, bool $model = false): bool
    {
        if (! $this->chunks()) {
            return false;
        }

        return $this->chunksById()
            ? $builder->chunkById($this->getChunkSize(), $this->provideChunkCallback($callback, $model))
            : $builder->chunk($this->getChunkSize(), $this->provideChunkCallback($callback, $model));
    }

    /**
     * Provide the chunk callback.
     */
    private function provideChunkCallback(callable $callback, bool $model = false): \Closure
    {
        return $model
            ? function (Collection $records) use ($callback) {
                foreach ($records as $record) {
                    \call_user_func($callback, $record);
                }
            }
        : function (Collection $records) use ($callback) {
            \call_user_func($callback, $records);
        };
    }
}
