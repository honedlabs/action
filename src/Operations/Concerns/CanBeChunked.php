<?php

declare(strict_types=1);

namespace Honed\Action\Operations\Concerns;

use Closure;
use Honed\Action\Contracts\ShouldChunk;
use Honed\Action\Contracts\ShouldChunkById;
use Illuminate\Database\Eloquent\Builder;

/**
 * @phpstan-require-extends \Honed\Action\Operations\Operation
 */
trait CanBeChunked
{
    public const CHUNK_SIZE = 500;

    /**
     * Whether the action should be chunked.
     */
    protected bool $chunk = false;

    /**
     * Whether the bulk action should chunk the records by id.
     */
    protected bool $chunkById = false;

    /**
     * The size of the chunk to use when chunking the records.
     */
    protected int $chunkSize = self::CHUNK_SIZE;

    /**
     * Set the action to chunk the records.
     *
     * @return $this
     */
    public function chunk(bool $value = true): static
    {
        $this->chunk = $value;

        return $this;
    }

    /**
     * Set the action to not chunk the records.
     *
     * @return $this
     */
    public function dontChunk(bool $value = true): static
    {
        return $this->chunk(! $value);
    }

    /**
     * Determine if the action should use chunking.
     */
    public function isChunked(): bool
    {
        return $this->chunk || $this instanceof ShouldChunk;
    }

    /**
     * Determine if the action should not use chunking.
     */
    public function isNotChunked(): bool
    {
        return ! $this->isChunked();
    }

    /**
     * Set the action to chunk the records by id.
     *
     * @return $this
     */
    public function chunkById(bool $value = true): static
    {
        $this->chunkById = $value;

        return $this->chunk();
    }

    /**
     * Set the action to not chunk the records by id.
     *
     * @return $this
     */
    public function dontChunkById(bool $value = true): static
    {
        return $this->chunkById(! $value);
    }

    /**
     * Determine if the action should chunk the records by id.
     */
    public function isChunkedById(): bool
    {
        return $this->chunkById || $this instanceof ShouldChunkById;
    }

    /**
     * Determine if the action should not chunk the records by id.
     */
    public function isNotChunkedById(): bool
    {
        return ! $this->isChunkedById();
    }

    /**
     * Set the size of the chunk to use when chunking the records.
     *
     * @return $this
     */
    public function chunkSize(int $size): static
    {
        $this->chunkSize = $size;

        return $this;
    }

    /**
     * Get the size of the chunk to use when chunking the records.
     */
    public function getChunkSize(): int
    {
        return $this->chunkSize;
    }

    /**
     * Execute the inline action on the given record.
     */
    public function callback(): ?Closure
    {
        $handler = $this->getHandler();

        if (! $handler) {
            return null;
        }

        return match (true) {
            $this->isChunkedById() => fn (Builder $builder) => $builder->chunkById($this->getChunkSize(), $handler),
            $this->isChunked() => fn (Builder $builder) => $builder->chunk($this->getChunkSize(), $handler),
            default => $handler,
        };
    }
}
