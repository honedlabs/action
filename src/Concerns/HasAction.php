<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Closure;
use Honed\Action\Contracts\Actionable;
use Honed\Core\Concerns\HasParameterNames;
use Illuminate\Support\Facades\App;

trait HasAction
{
    /**
     * @use HasParameterNames<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>>
     */
    use HasParameterNames;

    /**
     * The action handler.
     *
     * @var \Closure|class-string<\Honed\Action\Contracts\Actionable>|null
     */
    protected $action;

    /**
     * Set the action handler.
     *
     * @param  \Closure|class-string<\Honed\Action\Contracts\Actionable>  $action
     * @return $this
     */
    public function action($action)
    {
        $this->action = $action;

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
     * Determine if the instance has an action handler.
     *
     * @return bool
     */
    public function hasAction()
    {
        return isset($this->action) || $this instanceof Actionable;
    }

    /**
     * Get the handler for the actionable class.
     *
     * @return \Closure|null
     */
    protected function getHandler()
    {
        $action = $this->getAction();

        return match (true) {
            \is_string($action) => Closure::fromCallable([
                type(App::make($action))->as(Actionable::class), 'handle',
            ]),
            $this instanceof Actionable => Closure::fromCallable([
                $this, 'handle',
            ]),
            default => $action,
        };
    }
}
