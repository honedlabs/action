<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Contracts\HasHandler;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait HasAction
{
    /**
     * @var \Closure|null
     */
    protected $action;

    /**
     * Execute the action handler using the provided data.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\RedirectResponse|void
     */
    abstract public function execute($data);

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
     * @return bool
     */
    public function hasAction()
    {
        return ! \is_null($this->action) || $this instanceof HasHandler;
    }

    /**
     * Retrieve the parameter names for the action.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  T|\Illuminate\Database\Eloquent\Builder<T>  $parameter
     * @return array{0: T, 1: string, 2: string}
     */
    public function getActionParameterNames(Builder|Model $parameter): array
    {
        $model = $parameter instanceof Builder
            ? $parameter->getModel()
            : $parameter;

        $table = $model->getTable();

        return [
            $model,
            str($table)->singular()->camel()->toString(),
            str($table)->camel()->toString(),
        ];
    }
}
