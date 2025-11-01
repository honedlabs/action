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
        $this->before($payload);

        $attributes = $this->attributes($payload);

        $event = $this->dispatch();

        $event::dispatch($attributes);

        $this->after($attributes, $event);

        return $attributes;
    }

    /**
     * Prepare the payload for dispatching.
     *
     * @param  TPayload  $payload
     * @return TInput
     */
    public function attributes($payload)
    {
        /** @var TInput */
        return $payload;
    }

    /**
     * Perform additional logic before the action has been executed.
     *
     * @param  TPayload  $payload
     */
    public function before($payload): void {}

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TInput  $payload
     * @param  class-string<TDispatch>  $dispatch
     */
    public function after($payload, $dispatch): void {}
}
