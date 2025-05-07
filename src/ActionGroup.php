<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HasActions;
use Honed\Action\Concerns\HasEncoder;
use Honed\Action\Concerns\HasEndpoint;
use Honed\Action\Contracts\Handles;
use Honed\Action\Support\Constants;
use Honed\Core\Concerns\HasResource;
use Honed\Core\Primitive;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Support\Str;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 */
class ActionGroup extends Primitive implements Handles, UrlRoutable
{
    /**
     * @use \Honed\Action\Concerns\HasActions<TModel, TBuilder>
     */
    use HasActions;

    use HasEncoder;
    use HasEndpoint;

    /**
     * @use \Honed\Core\Concerns\HasResource<TModel, TBuilder>
     */
    use HasResource;

    /**
     * The model to be used to resolve inline actions.
     *
     * @var TModel|null
     */
    protected $model;

    /**
     * The default namespace where action groups reside.
     *
     * @var string
     */
    public static $namespace = 'App\\ActionGroups\\';

    /**
     * How to resolve the action group for the given model name.
     *
     * @var (\Closure(class-string):class-string<\Honed\Action\ActionGroup>)|null
     */
    protected static $actionGroupNameResolver;

    /**
     * Create a new action group instance.
     *
     * @param  \Honed\Action\Action|iterable<int, \Honed\Action\Action>  ...$actions
     * @return static
     */
    public static function make(...$actions)
    {
        return resolve(static::class)
            ->actions($actions);
    }

    /**
     * Set the model to be used to resolve inline actions.
     *
     * @param  TModel|null  $model
     * @return $this
     */
    public function for($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the model to be used to resolve inline actions.
     *
     * @return TModel|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * The root parent class.
     *
     * @return class-string
     */
    public static function baseClass()
    {
        return ActionGroup::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteKeyName()
    {
        return 'action';
    }

    /**
     * {@inheritdoc}
     */
    public function handle($request)
    {
        if ($this->isntExecutable()) {
            abort(404);
        }

        try {
            $resource = $this->getResource();

            return Handler::make(
                $resource,
                $this->getActions()
            )->handle($request);
        } catch (\RuntimeException $e) {
            abort(404);
        }
    }

    /**
     * Get a new table instance for the given model name.
     *
     * @template TClass of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TClass>  $modelName
     * @return \Honed\Action\ActionGroup<TClass>
     */
    public static function actionsForModel($modelName)
    {
        $table = static::resolveActionGroupName($modelName);

        return $table::make();
    }

    /**
     * Get the table name for the given model name.
     *
     * @param  class-string  $className
     * @return class-string<\Honed\Action\ActionGroup>
     */
    public static function resolveActionGroupName($className)
    {
        $resolver = static::$actionGroupNameResolver ?? function (string $className) {
            $appNamespace = static::appNamespace();

            $className = Str::startsWith($className, $appNamespace.'Models\\')
                ? Str::after($className, $appNamespace.'Models\\')
                : Str::after($className, $appNamespace);

            /** @var class-string<\Honed\Action\ActionGroup> */
            return static::$namespace.$className.'Actions';
        };

        return $resolver($className);
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
        } catch (\Throwable) {
            return 'App\\';
        }
    }

    /**
     * Specify the default namespace that contains the application's model action groups.
     *
     * @param  string  $namespace
     * @return void
     */
    public static function useNamespace($namespace)
    {
        static::$namespace = $namespace;
    }

    /**
     * Specify the callback that should be invoked to guess the name of a model action group.
     *
     * @param  \Closure(class-string):class-string<\Honed\Action\ActionGroup>  $callback
     * @return void
     */
    public static function guessTableNamesUsing($callback)
    {
        static::$actionGroupNameResolver = $callback;
    }

    /**
     * Flush the action group's global configuration state.
     *
     * @return void
     */
    public static function flushState()
    {
        static::$actionGroupNameResolver = null;
        static::$namespace = 'App\\ActionGroups\\';
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $actions = [
            Constants::INLINE => $this->inlineActionsToArray($this->getModel()),
            Constants::BULK => $this->bulkActionsToArray(),
            Constants::PAGE => $this->pageActionsToArray(),
        ];

        if ($this->isExecutable(ActionGroup::class)) {
            return \array_merge($actions, [
                'id' => $this->getRouteKey(),
                'endpoint' => $this->getEndpoint(),
            ]);
        }

        return $actions;
    }
}
