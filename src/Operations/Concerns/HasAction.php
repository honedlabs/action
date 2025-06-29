<?php

declare(strict_types=1);

namespace Honed\Action\Operations\Concerns;

use Closure;
use Honed\Action\Contracts\Action;
use Illuminate\Support\Facades\App;

use function is_string;

trait HasAction
{
    /**
     * The action handler.
     *
     * @var Closure|class-string<Action>|null
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
     * @param  Closure|class-string<Action>  $action
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
     * @return Closure|class-string<Action>|null
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
    public function hasAction()
    {
        return (bool) $this->action || $this instanceof Action;
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
     * @return Closure|null
     */
    public function getHandler()
    {
        $action = $this->getAction();

        return match (true) {
            // @phpstan-ignore-next-line argument.type
            is_string($action) => Closure::fromCallable([
                App::make($action),
                'handle',
            ]),
            $this instanceof Action => Closure::fromCallable([
                $this, 'handle',
            ]),
            default => $action,
        };
    }
}
