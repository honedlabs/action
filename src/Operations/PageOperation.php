<?php

declare(strict_types=1);

namespace Honed\Action\Operations;

use Closure;
use Honed\Action\Concerns\CanChunk;
use Illuminate\Database\Eloquent\Builder;

class PageOperation extends Operation
{
    use CanChunk;

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
