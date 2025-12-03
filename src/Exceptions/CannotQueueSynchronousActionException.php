<?php

declare(strict_types=1);

namespace Honed\Action\Exceptions;

use Exception;
use Honed\Action\Contracts\Action;

class CannotQueueSynchronousActionException extends Exception
{
    /**
     * Create a new exception.
     *
     * @param  class-string<Action>|Action  $action
     */
    final public function __construct(string|Action $action)
    {
        $className = is_string($action) ? $action : get_class($action);

        parent::__construct(
            "The action [{$className}] can only be run synchronously and is not queueable."
        );
    }

    /**
     * Throw the exception.
     *
     * @param  class-string<Action>|Action  $action
     *
     * @throws self
     */
    public static function throw(string|Action $action): never
    {
        throw new self($action);
    }
}
