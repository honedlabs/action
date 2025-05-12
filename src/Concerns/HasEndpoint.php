<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

trait HasEndpoint
{
    /**
     * Handle the incoming action request.
     *
     * @param  \Honed\Action\Http\Requests\InvokableRequest  $request
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    abstract public function handle($request);

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
     * Define the endpoint to execute server actions.
     *
     * @return string|null
     */
    public static function defineEndpoint()
    {
        return null;
    }

    /**
     * Get the endpoint to execute server actions.
     *
     * @return string|null
     */
    public function getEndpoint()
    {
        $endpoint = $this->endpoint ?? static::defineEndpoint();

        return $endpoint ?? static::getDefaultEndpoint();
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
     * Set whether the instance should execute server actions.
     *
     * @param  bool  $executes
     * @return $this
     */
    public function executes($executes = true)
    {
        $this->execute = $executes;

        return $this;
    }

    /**
     * Set the instance to execute server actions.
     *
     * @return $this
     */
    public function shouldExecute()
    {
        return $this->executes(true);
    }

    /**
     * Set the instance to not execute server actions.
     *
     * @return $this
     */
    public function shouldNotExecute()
    {
        return $this->executes(false);
    }

    /**
     * Set the instance to not execute server actions.
     *
     * @return $this
     */
    public function shouldntExecute()
    {
        return $this->shouldNotExecute();
    }

    /**
     * Determine if the instance can execute server actions.
     *
     * @param  class-string|null  $class
     * @return bool
     */
    public function isExecutable($class = null)
    {
        if ($class === null) {
            return $this->execute;
        }

        // @phpstan-ignore-next-line
        return $this->execute && \is_subclass_of($this::class, $class);
    }

    /**
     * Determine if the instance cannot execute server actions.
     *
     * @param  class-string|null  $class
     * @return bool
     */
    public function isNotExecutable($class = null)
    {
        return ! $this->isExecutable($class);
    }

    /**
     * Determine if the instance cannot execute server actions.
     *
     * @param  class-string|null  $class
     * @return bool
     */
    public function isntExecutable($class = null)
    {
        return $this->isNotExecutable($class);
    }
}
