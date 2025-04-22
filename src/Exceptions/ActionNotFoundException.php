<?php

declare(strict_types=1);

namespace Honed\Action\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActionNotFoundException extends NotFoundHttpException
{
    /**
     * Create a new action not found exception.
     *
     * @param  string  $name
     */
    public function __construct($name)
    {
        parent::__construct(
            \sprintf(
                'No action named [%s] exists for the provided handler.',
                $name
            )
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
