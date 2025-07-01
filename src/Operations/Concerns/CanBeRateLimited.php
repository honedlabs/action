<?php

declare(strict_types=1);

namespace Honed\Action\Operations\Concerns;

use Closure;
use Illuminate\Support\Facades\Auth;

trait CanBeRateLimited
{
    /**
     * The number of attempts allowed within a minute.
     *
     * @var int|(Closure(mixed...):int|null)|null
     */
    protected int|Closure|null $rateLimit = null;

    /**
     * The key to use for the rate limit.
     *
     * @var string|(Closure(mixed...):string|null)
     */
    protected string|Closure|null $rateLimitBy = null;

    /**
     * Set the number of attempts allowed within a minute.
     *
     * @param  int|(Closure(mixed...):int|null)|null  $attempts
     * @return $this
     */
    public function rateLimit(int|Closure|null $attempts): static
    {
        $this->rateLimit = $attempts;

        return $this;
    }

    /**
     * Disable rate limiting.
     *
     * @return $this
     */
    public function dontRateLimit(): static
    {
        return $this->rateLimit(null);
    }

    /**
     * Get the number of attempts allowed within a minute.
     */
    public function getRateLimit(): ?int
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
    public function rateLimitBy(string|Closure|null $key): static
    {
        $this->rateLimitBy = $key;

        return $this;
    }

    /**
     * Get the key to use for the rate limit.
     *
     * @param  array<string,mixed>  $named
     * @param  array<class-string,mixed>  $typed
     */
    public function getRateLimitBy(array $named = [], array $typed = []): string
    {
        return $this->evaluate($this->rateLimitBy, $named, $typed)
            ?? static::class.':'.(string) Auth::id();
    }
}
