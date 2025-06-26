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
     *
     * @var bool
     */
    protected $chunk = false;

    /**
     * Whether the bulk action should chunk the records by id.
     *
     * @var bool
     */
    protected $chunkById = false;

    /**
     * The size of the chunk to use when chunking the records.
     *
     * @var int
     */
    protected $chunkSize = self::CHUNK_SIZE;

    /**
     * Set the action to chunk the records.
     *
     * @param  bool  $value
     * @return $this
     */
    public function chunk($value = true)
    {
        $this->chunk = $value;

        return $this;
    }

    /**
     * Set the action to not chunk the records.
     *
     * @param  bool  $value
     * @return $this
     */
    public function dontChunk($value = true)
    {
        return $this->chunk(! $value);
    }

    /**
     * Determine if the action should use chunking.
     *
     * @return bool
     */
    public function isChunked()
    {
        return $this->chunk || $this instanceof ShouldChunk;
    }

    /**
     * Determine if the action should not use chunking.
     *
     * @return bool
     */
    public function isNotChunked()
    {
        return ! $this->isChunked();
    }

    /**
     * Set the action to chunk the records by id.
     *
     * @param  bool  $value
     * @return $this
     */
    public function chunkById($value = true)
    {
        $this->chunkById = $value;

        return $this->chunk();
    }

    /**
     * Set the action to not chunk the records by id.
     *
     * @param  bool  $value
     * @return $this
     */
    public function dontChunkById($value = true)
    {
        return $this->chunkById(! $value);
    }

    /**
     * Determine if the action should chunk the records by id.
     *
     * @return bool
     */
    public function isChunkedById()
    {
        return $this->chunkById || $this instanceof ShouldChunkById;
    }

    /**
     * Determine if the action should not chunk the records by id.
     *
     * @return bool
     */
    public function isNotChunkedById()
    {
        return ! $this->isChunkedById();
    }

    /**
     * Set the size of the chunk to use when chunking the records.
     *
     * @param  int  $size
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
        return $this->chunkSize;
    }

    /**
     * Execute the inline action on the given record.
     *
     * @return Closure|null
     */
    public function callback()
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
