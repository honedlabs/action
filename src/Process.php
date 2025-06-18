<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\CanBeTransaction;
use Illuminate\Support\Facades\Pipeline;
use Throwable;

/**
 * @template TPayload
 * @template TResult
 */
abstract class Process
{
    use CanBeTransaction;

    /**
     * The tasks to be sequentially executed.
     *
     * @return array<int, class-string>
     */
    abstract protected function tasks();

    /**
     * Run the process with exception handling.
     *
     * @param  TPayload  $payload
     * @return TResult
     */
    public function run($payload)
    {
        try {
            return $this->handle($payload);
        } catch (Throwable $e) {
            return $this->failure($e);
        }
    }

    /**
     * Handle the process without exception handling.
     *
     * @param  TPayload  $payload
     * @return TResult
     */
    public function handle($payload)
    {
        return $this->transact(
            fn () => $this->pipe($payload)
        );
    }

    /**
     * Handle the failure of the process.
     *
     * @param  Throwable  $throwable
     * @return mixed
     */
    protected function failure($throwable)
    {
        //
    }

    /**
     * The method to call on each pipe.
     *
     * @return string
     */
    protected function method()
    {
        return 'handle';
    }

    /**
     * Execute the pipeline.
     *
     * @param  TPayload  $payload
     * @return TResult
     */
    protected function pipe($payload)
    {
        return Pipeline::send($payload)
            ->through($this->tasks())
            ->via($this->method())
            ->thenReturn();
    }
}
