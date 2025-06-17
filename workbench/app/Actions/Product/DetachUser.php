<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Presets\DetachAction;

/**
 * @template TModel of \Workbench\App\Models\Product
 * @template TUser of \Workbench\App\Models\User
 *
 * @extends \Honed\Action\Presets\DetachAction<TModel, TUser>
 */
class DetachUser extends DetachAction
{
    /**
     * Get the relation name, must be a belongs to many relationship.
     *
     * @return string
     */
    protected function relationship()
    {
        return 'users';
    }
}
