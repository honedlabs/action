<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\Transactable;
use Honed\Action\Contracts\Action;
use Honed\Core\Concerns\HasContainer;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Pipeline;
use Throwable;

use function array_map;

/**
 * @template TPayload
 * @template TResult
 */
abstract class Process implements Action
{
    use HasContainer;
    use Transactable;

    /**
     * Create a new class instance.
     */
    public function __construct(Container $container)
    {
        $this->container($container);
    }

    /**
     * The tasks to be sequentially executed.
     *
     * @return array<int, class-string>
     */
    abstract protected function tasks(): array;

    /**
     * Create a new instance of the process.
     */
    public static function make(): static
    {
        return resolve(static::class);
    }

    /**
     * Handle the process with exception handling.
     *
     * @param  TPayload  $payload
     * @return TResult
     */
    public function handle($payload)
    {
        try {
            return $this->run($payload);
        } catch (Throwable $e) {
            return $this->failure($e);
        }
    }

    /**
     * Run the process without exception handling.
     *
     * @param  TPayload  $payload
     * @return TResult
     */
    public function run($payload)
    {
        return $this->transact(
            fn () => $this->pipe($payload)
        );
    }

    /**
     * Handle the failure of the process.
     *
     * @return mixed
     */
    protected function failure(Throwable $throwable)
    {
        return false;
    }

    /**
     * The method to call on each pipe.
     */
    protected function method(): string
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
            ->through($this->pipes())
            ->via($this->method())
            ->thenReturn();
    }

    /**
     * Generate the pipelines with closures.
     *
     * @return array<int, callable>
     */
    protected function pipes(): array
    {
        return array_map(
            fn ($task) => is_callable($task)
                ? $task
                : fn ($payload, $next) => $next(
                    $this->getContainer()
                        ->make($task)
                        ->{$this->method()}($payload)
                ),
            $this->tasks()
        );
    }
}
