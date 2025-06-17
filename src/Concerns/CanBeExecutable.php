<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

trait CanBeExecutable
{
    /**
     * Whether the instance can execute server actions.
     *
     * @var bool
     */
    protected $executable = true;

    /**
     * Set whether the instance can execute server actions.
     *
     * @param  bool  $executable
     * @return $this
     */
    public function executable($executable = true)
    {
        $this->executable = $executable;

        return $this;
    }

    /**
     * Set whether the instance cannot execute server actions.
     *
     * @param  bool  $executable
     * @return $this
     */
    public function notExecutable($executable = true)
    {
        return $this->executable(! $executable);
    }

    /**
     * Determine if the instance can execute server actions.
     *
     * @param  class-string|null  $class
     * @return bool
     */
    public function isExecutable($class = null)
    {
        return $this->executable;
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
}
