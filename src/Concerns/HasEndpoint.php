<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

trait HasEndpoint
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
    protected $execute = true;

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
     * @return string|null
     */
    public function getEndpoint()
    {
        return $this->endpoint ?? static::getDefaultEndpoint();
    }

    /**
     * Get the default endpoint to execute server actions.
     *
     * @return string|null
     */
    public static function getDefaultEndpoint()
    {
        /** @var string|null */
        return config('action.endpoint', '/actions');
    }

    /**
     * Set the instance to execute server actions.
     *
     * @param  bool  $execute
     * @return $this
     */
    public function shouldExecute($execute = true)
    {
        $this->execute = $execute;

        return $this;
    }

    /**
     * Set the instance to not execute server actions.
     *
     * @param  bool  $execute
     * @return $this
     */
    public function shouldNotExecute($execute = true)
    {
        return $this->shouldExecute(! $execute);
    }

    /**
     * Determine if the instance can execute server actions.
     *
     * @param  class-string  $class
     * @return bool
     */
    public function canExecuteServerActions($class)
    {
        // @phpstan-ignore-next-line
        return $this->execute && \is_subclass_of($this, $class);
    }

    /**
     * Determine if the instance cannot execute server actions.
     *
     * @param  class-string  $class
     * @return bool
     */
    public function cannotExecuteServerActions($class)
    {
        return ! $this->canExecuteServerActions($class);
    }
}
