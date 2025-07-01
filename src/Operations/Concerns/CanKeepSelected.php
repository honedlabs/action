<?php

declare(strict_types=1);

namespace Honed\Action\Operations\Concerns;

trait CanKeepSelected
{
    /**
     * Whether the action keeps the records selected after successful execution.
     */
    protected bool $keepSelected = false;

    /**
     * Set the action to keep the records selected.
     *
     * @return $this
     */
    public function keepSelected(bool $value = true): static
    {
        $this->keepSelected = $value;

        return $this;
    }

    /**
     * Determine if the action keeps the records selected.
     */
    public function keepsSelected(): bool
    {
        return $this->keepSelected;
    }
}
