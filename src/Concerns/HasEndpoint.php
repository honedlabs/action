<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use function is_subclass_of;

trait HasEndpoint
{
    /**
     * The endpoint to execute server actions.
     *
     * @var string|null
     */
    protected $endpoint;

    /**
     * Get the default endpoint to execute server actions.
     *
     * @return string
     */
    public static function getDefaultEndpoint()
    {
        /** @var string */
        return config('action.endpoint', '/actions');
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
     * @return string|null
     */
    public function getEndpoint()
    {
        return $this->endpoint ?? static::getDefaultEndpoint();
    }
}
