<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

/**
 * @phpstan-require-implements \Honed\Action\Contracts\HandlesOperations
 */
trait Actionable
{
    /**
     * The endpoint to execute server actions.
     *
     * @var string|null
     */
    protected $endpoint;

    /**
     * Whether the instance can execute server actions.
     *
     * @var bool
     */
    protected $actionable = true;

    /**
     * Get the parent class for the instance.
     *
     * @return class-string<\Honed\Action\Contracts\HandlesOperations>
     */
    public static function getParentClass()
    {
        return self::class;
    }

    /**
     * Get the default endpoint to execute server actions.
     *
     * @return string
     */
    public static function getDefaultEndpoint()
    {
        /** @var string */
        return config('action.endpoint', 'actions');
    }

    /**
     * Set the endpoint to execute server actions.
     *
     * @param  string|null  $endpoint
     * @return $this
     */
    public function endpoint($endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Get the endpoint to execute server actions.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint ?? static::getDefaultEndpoint();
    }

    /**
     * Set whether the instance can execute server actions.
     *
     * @param  bool  $value
     * @return $this
     */
    public function actionable($value = true)
    {
        $this->actionable = $value;

        return $this;
    }

    /**
     * Set whether the instance cannot execute server actions.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notActionable($value = true)
    {
        return $this->actionable(! $value);
    }

    /**
     * Determine if the instance can execute server actions.
     *
     * @return bool
     */
    public function isActionable()
    {
        return $this->actionable
            && is_subclass_of($this, static::getParentClass());
    }

    /**
     * Determine if the instance cannot execute server actions.
     *
     * @return bool
     */
    public function isNotActionable()
    {
        return ! $this->isActionable();
    }
}
