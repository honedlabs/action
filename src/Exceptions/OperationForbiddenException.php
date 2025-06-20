<?php

declare(strict_types=1);

namespace Honed\Action\Exceptions;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OperationForbiddenException extends AccessDeniedHttpException
{
    /**
     * Create a new action not found exception.
     *
     * @param  string  $name
     */
    public function __construct($name)
    {
        parent::__construct(
            "The operation [{$name}] is not allowed.",
        );
    }

    /**
     * Throw a new action not found exception.
     *
     * @param  string  $name
     * @return never
     *
     * @throws static
     */
    public static function throw($name)
    {
        throw new self($name);
    }
}
