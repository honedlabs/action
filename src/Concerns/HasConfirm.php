<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Confirm;

trait HasConfirm
{
    /**
     * @var \Honed\Action\Confirm|null
     */
    protected $confirm;

    /**
     * Set the confirm for the instance.
     *
     * @param  \Honed\Action\Confirm|\Closure|string|null  $confirm
     * @param  string|null  $description
     * @return $this
     */
    public function confirm($confirm, $description = null)
    {
        match (true) {
            \is_null($confirm) => null,
            $confirm instanceof Confirm => $this->confirm = $confirm,
            $confirm instanceof \Closure => $this->evaluate($confirm),
            default => $this->confirmInstance()
                ->label($confirm)
                ->description($description)
        };

        return $this;
    }

    /**
     * Retrieve the confirm for the instance.
     *
     * @return \Honed\Action\Confirm|null
     */
    public function getConfirm()
    {
        return $this->confirm;
    }

    /**
     * Resolve the confirm using named and typed parameters.
     *
     * @param  array<string,mixed>  $parameters
     * @param  array<string,mixed>  $typed
     * @return $this
     */
    public function resolveConfirm($parameters = [], $typed = [])
    {
        $this->confirm?->resolve($parameters, $typed);

        return $this;
    }

    /**
     * Determine if the instance has a confirm.
     *
     * @return bool
     */
    public function hasConfirm()
    {
        return isset($this->confirm);
    }

    /**
     * Access the confirm for this instance.
     *
     * @return \Honed\Action\Confirm
     */
    protected function confirmInstance()
    {
        return $this->confirm ??= Confirm::make();
    }
}
