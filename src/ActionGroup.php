<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HasActions;
use Honed\Action\Concerns\HasEncoder;
use Honed\Action\Concerns\HasEndpoint;
use Honed\Action\Contracts\Handles;
use Honed\Core\Concerns\HasResource;
use Honed\Core\Primitive;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
class ActionGroup extends Primitive implements UrlRoutable, Handles
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
        if ($this->isntActionable()) {
            abort(404);
        }

        $resource = $this->getResource();

        return Handler::make(
            $resource,
            $this->getActions()
        )->handle($request);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $actions = [
            'inline' => $this->inlineActionsToArray($this->getModel()),
            'bulk' => $this->bulkActionsToArray(),
            'page' => $this->pageActionsToArray(),
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
