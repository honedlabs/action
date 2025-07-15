<?php

declare(strict_types=1);

namespace Honed\Action;

use Closure;
use Honed\Action\Actions\Concerns\Transactable;
use Honed\Action\Contracts\Action;
use Honed\Core\Concerns\HasContainer;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Pipeline;
use Throwable;

use function array_map;

/**
 * @template TPayload
 * @template TResult = TPayload
 */
abstract class Process implements Action
{
    use HasContainer;
    use Transactable;

    /**
     * The strategy to use for the process.
     *
     * @var 'chain'|'drill'
     */
    protected $strategy = 'chain';

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
        return $this->transaction(
            fn () => $this->pipe($payload)
        );
    }

    /**
     * Set the strategy of the process.
     *
     * @param  'chain'|'drill'  $strategy
     * @return $this
     */
    public function strategy(string $strategy): static
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * Get the strategy of the process.
     */
    public function getStrategy(): string
    {
        return $this->strategy;
    }

    /**
     * Set the process to chain the payload of each task to the next.
     *
     * @return $this
     */
    public function chain(): static
    {
        $this->strategy = 'chain';

        return $this;
    }

    /**
     * Set the process to drill the initial payload to all tasks.
     *
     * @return $this
     */
    public function drill(): static
    {
        $this->strategy = 'drill';

        return $this;
    }

    /**
     * Determine if the process is drilling.
     */
    public function isDrilling(): bool
    {
        return $this->strategy === 'drill';
    }

    /**
     * Determine if the process is chaining.
     */
    public function isChaining(): bool
    {
        return $this->strategy === 'chain';
    }

    /**
     * Resolve the process from the container.
     */
    protected function resolve(): void
    {
        $this->container(App::make(Container::class));
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
                : $this->call($task),
            $this->tasks()
        );
    }

    /**
     * Call the task with the appropriate strategy.
     *
     * @param  class-string  $task
     */
    protected function call(string $task): Closure
    {
        if ($this->isDrilling()) {
            return $this->callDrill($task);
        }

        return $this->callChain($task);
    }

    /**
     * Call the task in a drilling strategy.
     *
     * @param  class-string  $task
     */
    protected function callDrill(string $task): Closure
    {
        return function ($payload, $next) use ($task) {
            $this->callTask($task, $payload);

            return $next($payload);
        };
    }

    /**
     * Call the task in a chaining strategy.
     *
     * @param  class-string  $task
     */
    protected function callChain(string $task): Closure
    {
        return fn ($payload, $next) => $next($this->callTask($task, $payload));
    }

    /**
     * Call the task.
     *
     * @param  class-string  $task
     * @param  TPayload  $payload
     * @return TResult
     */
    protected function callTask(string $task, $payload)
    {
        return $this->getContainer()
            ->make($task)
            ->{$this->method()}($payload);
    }
}
