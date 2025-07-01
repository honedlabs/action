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
     */
    protected ?Confirm $confirm = null;

    /**
     * Set the confirm for the instance.
     *
     * @param  string|null  $description
     * @return $this
     */
    public function confirmable(
        Confirm|Closure|string|bool $confirm = true,
        Closure|string|null $description = null
    ): static {

        match (true) {
            ! $confirm => $this->confirm = null,
            $confirm instanceof Confirm => $this->confirm = $confirm,
            $confirm instanceof Closure => $this->evaluate($confirm),
            default => $this->newConfirm()
                ->when(
                    is_string($confirm),
                    // @phpstan-ignore-next-line argument.type
                    fn (Confirm $c) => $c->title($confirm)
                )
                ->when(
                    $description !== null,
                    // @phpstan-ignore-next-line argument.type
                    fn (Confirm $confirm) => $confirm->description($description)
                )
        };

        return $this;
    }

    /**
     * Set the instance to not require confirmation.
     *
     * @return $this
     */
    public function notConfirmable(bool $value = true): static
    {
        return $this->confirmable(! $value);
    }

    /**
     * Retrieve the confirm for the instance.
     */
    public function getConfirm(): ?Confirm
    {
        return $this->confirm;
    }

    /**
     * Determine if the instance requires confirmation.
     */
    public function isConfirmable(): bool
    {
        return (bool) $this->confirm;
    }

    /**
     * Determine if the instance does not require confirmation.
     */
    public function isNotConfirmable(): bool
    {
        return ! $this->isConfirmable();
    }

    /**
     * Access the confirm for this instance.
     */
    protected function newConfirm(): Confirm
    {
        return $this->confirm ??= Confirm::make();
    }
}
