<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Contracts\Handles;

trait HasAction
{
    use HasParameterNames;

    /**
     * @var \Closure|null
     */
    protected $action;

    /**
     * Execute the action handler using the provided data.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model  $parameter
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\RedirectResponse|void
     */
    abstract public function execute($parameter);

    /**
     * Set the action handler.
     *
     * @return $this
     */
    public function action(?\Closure $action = null): static
    {
        if (! \is_null($action)) {
            $this->action = $action;
        }

        return $this;
    }

    /**
     * Get the action handler.
     */
    public function getAction(): ?\Closure
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
        return isset($this->action) || $this instanceof Handles;
    }
}
