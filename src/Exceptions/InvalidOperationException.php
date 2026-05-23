<?php

declare(strict_types=1);

namespace Honed\Action\Exceptions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidOperationException extends BadRequestHttpException
{
    /**
     * Create a new invalid operation exception.
     */
    public function __construct()
    {
        parent::__construct(
            'The provided operation is invalid, as the data cannot be resolved.'
        );
    }

    /**
     * Throw a new invalid operation exception.
     *
     * @throws self
     */
    public static function throw(): never
    {
        throw new self();
    }
}
