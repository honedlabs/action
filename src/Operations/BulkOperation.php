<?php

declare(strict_types=1);

namespace Honed\Action\Operations;

use Closure;
use Honed\Action\Concerns\CanChunk;
use Honed\Action\Operations\Concerns\CanKeepSelected;
use Illuminate\Database\Eloquent\Builder;

class BulkOperation extends Operation
{
    use CanChunk;
    use CanKeepSelected;

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

    /**
     * Get the representation of the instance.
     *
     * @return array<string, mixed>
     */
    protected function representation(): array
    {
        return [
            ...parent::representation(),
            'keep' => $this->keepsSelected(),
        ];
    }
}
