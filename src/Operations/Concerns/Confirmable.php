<?php

declare(strict_types=1);

namespace Honed\Action\Operations\Concerns;

use Closure;
use Honed\Action\Confirm;

/**
 * @phpstan-require-extends \Honed\Core\Primitive
 */
trait Confirmable
{
    /**
     * The instance of the confirm.
     *
     * @var Confirm|null
     */
    protected $confirm;

    /**
     * Set the confirm for the instance.
     *
     * @param  Confirm|Closure|string|bool  $confirm
     * @param  string|null  $description
     * @return $this
     */
    public function confirmable($confirm = true, $description = null)
    {
        match (true) {
            ! $confirm => $this->confirm = null,
            $confirm instanceof Confirm => $this->confirm = $confirm,
            $confirm instanceof Closure => $this->evaluate($confirm),
            default => $this->newConfirm()
                ->title(is_string($confirm) ? $confirm : 'Confirm action')
                ->description($description)
        };

        return $this;
    }

    /**
     * Set the instance to not require confirmation.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notConfirmable($value = true)
    {
        return $this->confirmable(! $value);
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
     * Determine if the instance requires confirmation.
     *
     * @return bool
     */
    public function isConfirmable()
    {
        return (bool) $this->confirm;
    }

    /**
     * Determine if the instance does not require confirmation.
     *
     * @return bool
     */
    public function isNotConfirmable()
    {
        return ! $this->isConfirmable();
    }

    /**
     * Access the confirm for this instance.
     *
     * @return Confirm
     */
    protected function newConfirm()
    {
        return $this->confirm ??= Confirm::make();
    }
}
