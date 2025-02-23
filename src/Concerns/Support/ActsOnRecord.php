<?php

declare(strict_types=1);

namespace Honed\Action\Concerns\Support;

trait ActsOnRecord
{
    /**
     * Determine whether the action should act on a retrieved record, or on the
     * underlying query/collection.
     *
     * @var bool
     */
    protected $retrieve = false;

    /**
     * Set the action to act on a retrieved record if true, or on the underlying
     * query/collection if false.
     *
     * @return $this
     */
    public function onRecord(bool $retrieve = true): static
    {
        $this->retrieve = $retrieve;

        return $this;
    }

    /**
     * Determine if the action should act on a retrieved record.
     */
    public function actsOnRecord(): bool
    {
        return $this->retrieve;
    }
}
