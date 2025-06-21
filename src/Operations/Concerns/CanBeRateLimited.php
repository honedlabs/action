<?php

declare(strict_types=1);

namespace Honed\Action\Operations\Concerns;

use Closure;

trait CanBeRateLimited
{
    /**
     * The number of attempts allowed within a minute.
     *
     * @var int|(Closure(mixed...):int|null)|null
     */
    protected $rateLimit = null;

    /**
     * Set the number of attempts allowed within a minute.
     *
     * @param  int|(Closure(mixed...):int|null)|null  $attempts
     * @return $this
     */
    public function rateLimit($attempts)
    {
        $this->rateLimit = $attempts;

        return $this;
    }

    /**
     * Get the number of attempts allowed within a minute.
     *
     * @return int|null
     */
    public function getRateLimit()
    {
        /** @var int|null */
        return $this->evaluate($this->rateLimit);
    }
}
