<?php

declare(strict_types=1);

namespace Honed\Action\Exceptions;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OperationForbiddenException extends AccessDeniedHttpException
{
    /**
     * Create a new action not found exception.
     */
    public function __construct(string $name)
    {
        parent::__construct(
            "The operation [{$name}] is not allowed.",
        );
    }

    /**
     * Throw a new action not found exception.
     *
     * @throws static
     */
    public static function throw(string $name): never
    {
        throw new self($name);
    }
}
