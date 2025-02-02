<?php

declare(strict_types=1);

namespace Honed\Action\Exceptions;

use Exception;

class InvalidActionException extends Exception
{
    public function __construct(string $name)
    {
        parent::__construct(
            \sprintf('Action [%s] not found.', $name),
            400
        );
    }
}
