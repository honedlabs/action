<?php

declare(strict_types=1);

namespace Honed\Action\Concerns\Support;

trait HasChunkSize
{
    /**
     * @var int|null
     */
    protected $chunkSize;

    public function chunkSize(int $size): static
    {
        $this->chunkSize = $size;

        return $this;
    }

    /**
     * Determine the chunk size.
     */
    public function getChunkSize(): int
    {
        return $this->chunkSize
            ?? type(config('action.chunk_size', 1000))->asInt();
    }
}
