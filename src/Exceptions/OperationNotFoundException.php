<?php

declare(strict_types=1);

namespace Honed\Action\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OperationNotFoundException extends NotFoundHttpException
{
    /**
     * Create a new action not found exception.
     */
    public function __construct(string $name)
    {
        parent::__construct(
            "No operation named [{$name}] exists for the provided handler.",
        );
    }

    /**
     * Throw a new action not found exception.
     *
     * @throws self
     */
    public static function throw(string $name): never
    {
        throw new self($name);
    }
}
