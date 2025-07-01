<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

/**
 * @phpstan-require-implements \Honed\Action\Contracts\HandlesOperations
 */
trait Actionable
{
    /**
     * Whether the instance can execute server actions.
     */
    protected bool $actionable = true;

    /**
     * Get the parent class for the instance.
     *
     * @return class-string<self>
     */
    public static function getParentClass(): string
    {
        return self::class;
    }

    /**
     * Get the default endpoint to execute server actions.
     */
    public static function getEndpoint(): string
    {
        /** @var string */
        $endpoint = config('action.endpoint', '_actions');

        return trim($endpoint, '/');
    }

    /**
     * Set whether the instance can execute server actions.
     *
     * @return $this
     */
    public function actionable(bool $value = true): static
    {
        $this->actionable = $value;

        return $this;
    }

    /**
     * Set whether the instance cannot execute server actions.
     *
     * @return $this
     */
    public function notActionable(bool $value = true): static
    {
        return $this->actionable(! $value);
    }

    /**
     * Determine if the instance can execute server actions.
     */
    public function isActionable(): bool
    {
        return $this->actionable
            && is_subclass_of($this, static::getParentClass());
    }

    /**
     * Determine if the instance cannot execute server actions.
     */
    public function isNotActionable(): bool
    {
        return ! $this->isActionable();
    }
}
