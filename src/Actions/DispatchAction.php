<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Contracts\Action;

/**
 * @template TDispatch of \Honed\Action\Contracts\Dispatches
 * @template TPayload of mixed
 */
abstract class DispatchAction implements Action
{
    /**
     * Get the dispatchable class.
     *
     * @return class-string<TDispatch>
     */
    abstract protected function dispatch();

    /**
     * Dispatch the payload.
     *
     * @param  TPayload  $payload
     * @return TPayload
     */
    public function handle($payload)
    {
        $event = $this->dispatch();

        $event::dispatch($payload);

        $this->after($payload, $event);

        return $payload;
    }

    /**
     * Perform additional logic after the event has been dispatched.
     *
     * @param  mixed  $payload
     * @param  class-string<TDispatch>  $dispatched
     * @return void
     */
    public function after($payload, $dispatched)
    {
        //
    }
}
