<?php

declare(strict_types=1);

namespace Honed\Action;

use Closure;
use Honed\Action\Concerns\CanResolveActions;
use Honed\Action\Contracts\Handler;
use Honed\Action\Handler as ActionHandler;
use Honed\Core\Primitive;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Throwable;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Batch extends Primitive
{
    use CanResolveActions;

    /**
     * The default namespace where action groups reside.
     */
    public static string $namespace = 'App\\Batches\\';

    /**
     * How to resolve the action group for the given model name.
     *
     * @var (Closure(class-string):class-string<Batch>)|null
     */
    protected static ?Closure $batchNameResolver = null;

    /**
     * Create a new action group instance.
     *
     * @param  Operation|Batch|array<int, Operation|Batch>  $operations
     */
    public static function make($operations = []): static
    {
        return resolve(static::class)
            ->operations($operations);
    }

    /**
     * Get a new table instance for the given model name.
     *
     * @template TClass of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TClass>  $modelName
     * @return Batch<TClass>
     */
    public static function batchForModel(string $modelName): self
    {
        $table = static::resolveBatchName($modelName);

        return $table::make();
    }

    /**
     * Get the table name for the given model name.
     *
     * @param  class-string  $className
     * @return class-string<Batch>
     */
    public static function resolveBatchName(string $className): string
    {
        $resolver = static::$batchNameResolver ?? function (string $className) {
            $appNamespace = static::appNamespace();

            $className = Str::startsWith($className, $appNamespace.'Models\\')
                ? Str::after($className, $appNamespace.'Models\\')
                : Str::after($className, $appNamespace);

            /** @var class-string<Batch> */
            return static::$namespace.$className.'Actions';
        };

        return $resolver($className);
    }

    /**
     * Specify the default namespace that contains the application's model action groups.
     */
    public static function useNamespace(string $namespace): void
    {
        static::$namespace = $namespace;
    }

    /**
     * Specify the callback that should be invoked to guess the name of a model action group.
     *
     * @param  Closure(class-string):class-string<Batch>  $callback
     */
    public static function guessBatchNamesUsing(Closure $callback): void
    {
        static::$batchNameResolver = $callback;
    }

    /**
     * Flush the action group's global configuration state.
     */
    public static function flushState(): void
    {
        static::$batchNameResolver = null;
        static::$namespace = 'App\\Batches\\';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteKeyName()
    {
        return 'action';
    }

    public function getHandler()
    {
        return config('action.handler', ActionHandler::class);
    }

    /**
     * {@inheritdoc}
     */
    public function handle($request)
    {
        if ($this->isNotExecutable()) {
            abort(404);
        }

        return App::make(Handler::class)
            ->handle($this, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $operations = [
            'inline' => $this->inlineActionsToArray($this->getModel()),
            'bulk' => $this->bulkActionsToArray(),
            'page' => $this->pageActionsToArray(),
        ];

        if ($this->isExecutable(self::class)) {
            return [
                ...$operations,
                'id' => $this->getRouteKey(),
                'endpoint' => $this->getEndpoint(),
            ];
        }

        return $operations;
    }

    /**
     * Get the application namespace for the application.
     *
     * @return string
     */
    protected static function appNamespace()
    {
        try {
            return Container::getInstance()
                ->make(Application::class)
                ->getNamespace();
        } catch (Throwable) {
            return 'App\\';
        }
    }

    /**
     * Define the operations for the action group.
     *
     * @param  $this  $operations
     * @return $this
     */
    protected function definition(self $operations): self
    {
        return $operations;
    }
}
