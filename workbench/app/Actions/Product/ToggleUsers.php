<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\ToggleAction;

/**
 * @template TModel of \Workbench\App\Models\Product
 * @template TUser of \Workbench\App\Models\User
 *
 * @extends \Honed\Action\Actions\ToggleAction<TModel, TUser>
 */
class ToggleUsers extends ToggleAction
{
    /**
     * Get the relation name, must be a belongs to many relationship.
     *
     * @return string
     */
    public function relationship()
    {
        return 'users';
    }
}
