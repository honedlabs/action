<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Contracts\Actionable;
use Illuminate\Support\Facades\App;

trait HasAction
{
    /**
     * The action handler.
     *
     * @var \Closure|class-string<\Honed\Action\Contracts\Actionable>|null
     */
    protected $action;

    /**
     * Optional parameters to pass to the Action handler.
     *
     * @var array<string,mixed>
     */
    protected $parameters = [];

    /**
     * Set the action handler.
     *
     * @param  \Closure|class-string<\Honed\Action\Contracts\Actionable>  $action
     * @param  array<string,mixed>  $parameters
     * @return $this
     */
    public function action($action, $parameters = [])
    {
        $this->action = $action;
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get the action handler.
     *
     * @return \Closure|class-string<\Honed\Action\Contracts\Actionable>|null
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Determine if the instance executes server actions.
     *
     * @return bool
     */
    public function isActionable()
    {
        return isset($this->action) || $this instanceof Actionable;
    }

    /**
     * Set optional parameters to pass to the Action handler.
     *
     * @param  array<string,mixed>  $parameters
     * @return $this
     */
    public function parameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get the parameters to pass to the Action handler.
     *
     * @return array<string,mixed>
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get the handler for the actionable class.
     *
     * @return \Closure|null
     */
    public function getHandler()
    {
        $action = $this->getAction();

        return match (true) {
            \is_string($action) => \Closure::fromCallable([
                type(App::make($action))->as(Actionable::class), 'handle',
            ]),
            $this instanceof Actionable => \Closure::fromCallable([
                $this, 'handle',
            ]),
            default => $action,
        };
    }
}
