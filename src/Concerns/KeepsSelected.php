<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

trait KeepsSelected
{
    protected bool $keepSelected = false;

    /**
     * Set the action to keep the records selected.
     *
     * @return $this
     */
    public function keepSelected(bool $keep = true): static
    {
        $this->keepSelected = $keep;

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
