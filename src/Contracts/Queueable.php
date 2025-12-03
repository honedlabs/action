<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

use Honed\Action\Jobs\QueuedAction;

/**
 * @template T of \Honed\Action\Jobs\QueuedAction = \Honed\Action\Jobs\QueuedAction
 */
interface Queueable
{
    /**
     * Create a new queued action instance.
     *
     * @return T
     */
    public function toJob(mixed ...$arguments): QueuedAction;

    /**
     * Configure the queued job for this action.
     *
     * @param  T  $job
     * @return T
     */
    public function configureJob(QueuedAction $job): QueuedAction;

    /**
     * Create a new queued action instance and configure it.
     *
     * @return T
     */
    public function queue(mixed ...$arguments): QueuedAction;
}
