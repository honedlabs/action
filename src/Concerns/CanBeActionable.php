<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

trait CanBeActionable
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
     * @param  bool  $actionable
     * @return $this
     */
    public function actionable($actionable = true)
    {
        $this->actionable = $actionable;

        return $this;
    }

    /**
     * Set whether the instance cannot execute server actions.
     *
     * @param  bool  $actionable
     * @return $this
     */
    public function notActionable($actionable = true)
    {
        return $this->actionable(! $actionable);
    }

    /**
     * Determine if the instance can execute server actions.
     *
     * @return bool
     */
    public function isActionable()
    {
        return $this->actionable;
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
