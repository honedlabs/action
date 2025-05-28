<?php

declare(strict_types=1);

namespace Honed\Action\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CouldNotResolveHandlerException extends NotFoundHttpException
{
    /**
     * Create a new could not resolve handler exception.
     */
    public function __construct()
    {
        parent::__construct(
            'No handler could be resolved.'
        );
    }

    /**
     * Throw a new could not resolve handler exception.
     *
     * @return never
     *
     * @throws static
     */
    public static function throw()
    {
        throw new self();
    }
}
