<?php

declare(strict_types=1);

namespace Honed\Action\Concerns\Support;

trait CanChunkById
{
    /**
     * @var bool|null
     */
    protected $chunkById;

    /**
     * Set the action to chunk the records by id.
     *
     * @return $this
     */
    public function chunkById(?bool $chunkById = true): static
    {
        if (! \is_null($chunkById)) {
            $this->chunkById = $chunkById;
        }

        return $this;
    }

    /**
     * Determine if the action should chunk the records by id.
     */
    public function chunksById(): bool
    {
        return $this->chunkById
            ?? (bool) config('action.chunk_by_id', true);
    }
}
