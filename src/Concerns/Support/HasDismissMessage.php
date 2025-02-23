<?php

declare(strict_types=1);

namespace Honed\Action\Concerns\Support;

trait HasDismissMessage
{
    /**
     * The message to display on the dismiss button.
     *
     * @var string|null
     */
    protected $dismiss;

    /**
     * Set the dismiss message for the confirm.
     *
     * @return $this
     */
    public function dismiss(?string $dismiss): static
    {
        if (! \is_null($dismiss)) {
            $this->dismiss = $dismiss;
        }

        return $this;
    }

    /**
     * Get the dismiss message for the confirm.
     */
    public function getDismiss(): string
    {
        if (\is_null($this->dismiss)) {
            return type(config('action.confirm.dismiss', 'Cancel'))->asString();
        }

        return $this->dismiss;
    }
}
