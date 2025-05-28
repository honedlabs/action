<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Closure;
use Honed\Action\Confirm;

use function is_null;

trait HasConfirm
{
    /**
     * @var Confirm|null
     */
    protected $confirm;

    /**
     * Set the confirm for the instance.
     *
     * @param  Confirm|Closure|string|null  $confirm
     * @param  string|null  $description
     * @return $this
     */
    public function confirm($confirm, $description = null)
    {
        match (true) {
            is_null($confirm) => null,
            $confirm instanceof Confirm => $this->confirm = $confirm,
            $confirm instanceof Closure => $this->evaluate($confirm),
            default => $this->confirmInstance()
                ->title($confirm)
                ->description($description)
        };

        return $this;
    }

    /**
     * Retrieve the confirm for the instance.
     *
     * @return Confirm|null
     */
    public function getConfirm()
    {
        return $this->confirm;
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
     * @return Confirm
     */
    protected function confirmInstance()
    {
        return $this->confirm ??= Confirm::make();
    }
}
