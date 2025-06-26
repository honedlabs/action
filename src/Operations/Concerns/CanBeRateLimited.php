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
     * The key to use for the rate limit.
     *
     * @var string|(Closure(mixed...):string|null)
     */
    protected $rateLimitBy;

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
     * Disable rate limiting.
     *
     * @return $this
     */
    public function dontRateLimit()
    {
        $this->rateLimit = null;

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

    /**
     * Set the key to use for the rate limit.
     *
     * @param  string|(Closure(mixed...):string|null)  $key
     * @return $this
     */
    public function rateLimitBy($key)
    {
        $this->rateLimitBy = $key;

        return $this;
    }

    /**
     * Get the key to use for the rate limit.
     *
     * @return string|null
     */
    public function getRateLimitBy()
    {
        /** @var string|null */
        return $this->evaluate($this->rateLimitBy);
    }
}
