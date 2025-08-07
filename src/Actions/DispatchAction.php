<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Contracts\Action;

/**
 * @template TDispatch
 * @template TPayload
 * @template TInput = TPayload
 */
abstract class DispatchAction implements Action
{
    /**
     * Get the dispatchable class.
     *
     * @return class-string<TDispatch>
     */
    abstract public function dispatch(): string;

    /**
     * Dispatch the payload.
     *
     * @param  TPayload  $payload
     * @return TInput
     */
    public function handle($payload = [])
    {
        $prepared = $this->prepare($payload);

        $event = $this->dispatch();

        $event::dispatch($prepared);

        $this->after($prepared, $event);

        return $prepared;
    }

    /**
     * Prepare the payload for dispatching.
     *
     * @param  TPayload  $payload
     * @return TInput
     */
    protected function prepare($payload)
    {
        /** @var TInput */
        return $payload;
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TInput  $payload
     * @param  class-string<TDispatch>  $dispatch
     */
    protected function after($payload, $dispatch): void
    {
        //
    }
}
