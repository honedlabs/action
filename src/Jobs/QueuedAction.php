<?php

declare(strict_types=1);

namespace Honed\Action\Jobs;

use Honed\Action\Attributes\Synchronous;
use Honed\Action\Contracts\Action;
use Honed\Action\Exceptions\CannotQueueSynchronousActionException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ReflectionClass;

/**
 * @template TAction of \Honed\Action\Contracts\Action
 */
class QueuedAction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The arguments for the action.
     *
     * @var array<array-key, mixed>
     */
    protected readonly array $arguments;

    /**
     * Create a new job instance.
     *
     * @param  class-string<TAction>  $action
     */
    public function __construct(
        protected readonly string|Action $action,
        mixed ...$arguments
    ) {
        $this->arguments = $arguments;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $action = is_string($this->action) ? app($this->action) : $this->action;

        if ($this->isSynchronous($action)) {
            $this->fail(
                new CannotQueueSynchronousActionException($action),
            );
        }

        $action->handle(...$this->arguments);
    }

    /**
     * Determine if the action can only be run synchronously.
     *
     * @param  TAction  $action
     */
    protected function isSynchronous(Action $action): bool
    {
        return filled(
            (new ReflectionClass($action))->getAttributes(Synchronous::class)
        );
    }
}
