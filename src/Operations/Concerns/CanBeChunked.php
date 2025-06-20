<?php

declare(strict_types=1);

namespace Honed\Action\Operations\Concerns;

use Closure;
use Honed\Action\Contracts\ShouldChunk;
use Honed\Action\Contracts\ShouldChunkById;
use Honed\Core\Concerns\HasQuery;
use Illuminate\Database\Eloquent\Builder;

/**
 * @phpstan-require-extends \Honed\Action\Operations\Operation
 */
trait CanBeChunked
{
    /** @use HasQuery<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>> */
    use HasQuery;

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
    protected $chunkSize = 500;

    /**
     * Set the action to chunk the records.
     *
     * @param  bool  $chunk
     * @return $this
     */
    public function chunk($chunk = true)
    {
        $this->chunk = $chunk;

        return $this;
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
     * Set the action to chunk the records by id.
     *
     * @param  bool  $byId
     * @return $this
     */
    public function chunkById($byId = true)
    {
        $this->chunkById = $byId;

        return $this->chunk();
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
