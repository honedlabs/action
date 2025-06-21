<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\Transactable;
use Honed\Action\Contracts\Action;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\Pipeline;
use RuntimeException;
use Throwable;

use function array_map;

/**
 * @template TPayload
 * @template TResult
 */
abstract class Process implements Action
{
    use Transactable;

    /**
     * The container implementation.
     *
     * @var Container|null
     */
    protected $container;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(?Container $container = null)
    {
        $this->container = $container;
    }

    /**
     * The tasks to be sequentially executed.
     *
     * @return array<int, class-string>
     */
    abstract protected function tasks();

    /**
     * Create a new instance of the process.
     *
     * @return static
     */
    public static function make()
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
     * Set the container instance.
     *
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Get the container instance.
     *
     * @return Container
     *
     * @throws RuntimeException
     */
    protected function getContainer()
    {
        if (! $this->container) {
            throw new RuntimeException('A container instance has not been passed to the Pipeline.');
        }

        return $this->container;
    }

    /**
     * Handle the failure of the process.
     *
     * @param  Throwable  $throwable
     * @return mixed
     */
    protected function failure($throwable)
    {
        return false;
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
            ->through($this->pipes())
            ->via($this->method())
            ->thenReturn();
    }

    /**
     * Generate the pipelines with closures.
     *
     * @return array<int, callable>
     */
    protected function pipes()
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
