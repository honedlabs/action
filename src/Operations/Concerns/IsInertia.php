<?php

declare(strict_types=1);

namespace Honed\Action\Operations\Concerns;

trait IsInertia
{
    /**
     * Whether the operation should be executed via Inertia.
     */
    protected bool $inertia = true;

    /**
     * Set whether the instance uses inertia.
     *
     * @return $this
     */
    public function inertia(bool $value = true): static
    {
        $this->inertia = $value;

        return $this;
    }

    /**
     * Set whether the instance does not use inertia.
     *
     * @return $this
     */
    public function notInertia(bool $value = true): static
    {
        return $this->inertia(! $value);
    }

    /**
     * Determine if the instance uses inertia.
     */
    public function isInertia(): bool
    {
        return $this->inertia;
    }

    /**
     * Determine if the instance does not use inertia.
     */
    public function isNotInertia(): bool
    {
        return ! $this->isInertia();
    }
}
