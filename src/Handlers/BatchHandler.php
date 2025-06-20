<?php

declare(strict_types=1);

namespace Honed\Action\Handlers;

/**
 * @extends Handler<\Honed\Action\Batch>
 */
class BatchHandler extends Handler
{
    /**
     * Get the key to use for selecting records.
     *
     * @return string
     */
    protected function getKey()
    {
        return $this->getInstance()->getKey() ?? 'id';
    }

    /**
     * Get the operations to be used to resolve the action.
     *
     * @return array<int,\Honed\Action\Operations\Operation>
     */
    protected function getOperations()
    {
        return $this->getInstance()->getOperations();
    }
}
