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
     * @return $this
     */
    public function confirm(Confirm|\Closure|string|null $confirm, ?string $description = null): static
    {
        if (! \is_null($confirm)) {
            match (true) {
                $confirm instanceof Confirm => $this->confirm = $confirm,
                $confirm instanceof \Closure => $this->evaluate($confirm),
                default => $this->confirmInstance()->name($confirm)->description($description)
            };
        }

        return $this;
    }

    /**
     * Get the confirm for this instance.
     */
    public function getConfirm(): ?Confirm
    {
        return $this->confirm;
    }

    /**
     * @param  array<string,mixed>  $parameters
     * @param  array<string,mixed>  $typed
     * 
     * @return $this
     */
    public function resolveConfirm(array $parameters = [], array $typed = []): static
    {
        $this->confirm?->resolve($parameters, $typed);

        return $this;
    }

    /**
     * Determine if the instance has a confirm.
     */
    public function hasConfirm(): bool
    {
        return ! \is_null($this->confirm);
    }

    /**
     * Access the confirm for this instance.
     */
    protected function confirmInstance(): Confirm
    {
        return $this->confirm ??= Confirm::make();
    }
}
