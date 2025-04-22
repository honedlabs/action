<?php

declare(strict_types=1);

namespace Honed\Action\Exceptions;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ActionNotAllowedException extends AccessDeniedHttpException
{
    /**
     * Create a new action not allowed exception.
     *
     * @param  string  $name
     */
    public function __construct($name)
    {
        parent::__construct(
            \sprintf(
                'The action [%s] is not allowed.',
                $name
            )
        );
    }

    /**
     * Throw a new action not allowed exception.
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
