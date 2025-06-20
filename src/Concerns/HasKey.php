<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

trait HasKey
{
    /**
     * The key to use for selecting records.
     *
     * @var string|null
     */
    protected $key;

    /**
     * Set the key to use for selecting records.
     *
     * @param  string|null  $key
     * @return $this
     */
    public function key($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the key to use for selecting records.
     *
     * @return string|null
     */
    public function getKey()
    {
        return $this->key;
    }
}
