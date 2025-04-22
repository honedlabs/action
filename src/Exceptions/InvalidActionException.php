<?php

declare(strict_types=1);

namespace Honed\Action\Exceptions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidActionException extends BadRequestHttpException
{
    /**
     * Create a new invalid action exception.
     *
     * @param  string  $type
     */
    public function __construct($type)
    {
        parent::__construct(
            \sprintf(
                'The provided action type [%s] is invalid.',
                $type
            )
        );
    }

    /**
     * Throw a new invalid action exception.
     *
     * @param  string  $type
     * @return never
     *
     * @throws \Honed\Action\Exceptions\InvalidActionException
     */
    public static function throw($type)
    {
        throw new self($type);
    }
}
