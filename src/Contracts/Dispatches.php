<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

interface Dispatches
{
    /**
     * Dispatch the event with the given arguments.
     *
     * @return mixed
     */
    public static function dispatch();
}
