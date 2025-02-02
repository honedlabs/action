<?php

declare(strict_types=1);

namespace Honed\Action\Exceptions;

class InvalidActionTypeException extends \Exception
{
    public function __construct(string $type)
    {
        parent::__construct(
            \sprintf('The provided action type [%s] is invalid.', $type)
        );
    }
}
