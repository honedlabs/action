<?php

declare(strict_types=1);

namespace Honed\Action\Concerns\Support;

use Honed\Action\Contracts\ShouldChunk;

trait IsChunked
{
    /**
     * Whether the action should be chunked.
     *
     * @var bool|null
     */
    protected $chunk;

    /**
     * Set the action to chunk the records.
     *
     * @return $this
     */
    public function chunk(bool $chunk = true): static
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

        if (! \is_null($this->chunk)) {
            return $this->chunk;
        }

        return (bool) config('action.chunk', false);
    }
}
