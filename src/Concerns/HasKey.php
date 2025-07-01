<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

trait HasKey
{
    /**
     * The key to use for selecting records.
     *
     * @var ?string
     */
    protected $key;

    /**
     * Set the key to use for selecting records.
     *
     * @return $this
     */
    public function key(?string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the key to use for selecting records.
     */
    public function getKey(): ?string
    {
        return $this->key;
    }
}
