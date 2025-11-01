<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Contracts\Action as ActionContract;

abstract class Action implements ActionContract
{
    /**
     * Create a new instance of the action.
     */
    public static function make(): static
    {
        return resolve(static::class);
    }
}
