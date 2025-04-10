<?php

declare(strict_types=1);

namespace Honed\Action;

use Honed\Action\Concerns\HasActions;
use Honed\Action\Concerns\HasEncoder;
use Honed\Action\Concerns\HasEndpoint;
use Honed\Action\Contracts\Handles;
use Honed\Core\Primitive;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ActionGroup extends Primitive implements UrlRoutable, Handles
{
    use HasActions;
    use HasEncoder;
    use HasEndpoint;

    /**
     * The builder to be used to resolve inline actions.
     *
     * @var \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model|null
     */
    protected $resource;

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
     * Set the model to be used to resolve inline actions.
     *
     * @param  \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $resource
     * @return $this
     */
    public function resource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get the resource to be used to resolve the actions.
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|null
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($request)
    {
        $resource = $this->getResource();

        if ($resource instanceof Builder) {
            return Handler::make(
                $this->getResource(),
                $this->getActions()
            )->handle($request);
        }

        return back();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $resource = $this->getResource();

        if (! $resource instanceof Model) {
            $resource = null;
        }

        $actions = [
            'inline' => $this->inlineActionsToArray($resource),
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
