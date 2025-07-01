<?php

declare(strict_types=1);

namespace Honed\Action\Handlers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Handler<\Honed\Action\Batch>
 */
class BatchHandler extends Handler
{
    /**
     * Get the resource for the handler.
     *
     * @return array<array-key, mixed>|Builder<Model>
     */
    protected function getResource(): array|Builder
    {
        return $this->getInstance()->getBuilder();
    }

    /**
     * Get the key to use for selecting records.
     */
    protected function getKey(): string
    {
        return $this->getInstance()->getKey() ?? 'id';
    }
}
