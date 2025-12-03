<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Jobs\QueuedAction;
use Illuminate\Foundation\Bus\PendingDispatch;

/**
 * @template T of \Honed\Action\Jobs\QueuedAction = \Honed\Action\Jobs\QueuedAction
 *
 * @phpstan-require-implements \Honed\Action\Contracts\Action
 * @phpstan-require-implements \Honed\Action\Contracts\Queueable
 */
trait Queueable
{
    /**
     * Create a new queued action instance.
     *
     * @return T
     */
    public function toJob(mixed ...$arguments): QueuedAction
    {
        return new QueuedAction($this, ...$arguments);
    }

    /**
     * Configure the queued job for this action.
     *
     * @param  T  $job
     * @return T
     */
    public function configureJob(QueuedAction $job): QueuedAction
    {
        return $job;
    }

    /**
     * Create a new queued action instance and configure it.
     *
     * @return T
     */
    public function queue(mixed ...$arguments): QueuedAction
    {
        return $this->configureJob($this->toJob(...$arguments));
    }

    /**
     * Dispatch the action on the queue.
     */
    public function dispatch(mixed ...$arguments): PendingDispatch
    {
        return new PendingDispatch($this->queue(...$arguments));
    }
}
