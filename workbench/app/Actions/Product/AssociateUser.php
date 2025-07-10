<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\AssociateAction;

/**
 * @template TModel of \Workbench\App\Models\Product
 * @template TParent of \Workbench\App\Models\User
 *
 * @extends \Honed\Action\Actions\AssociateAction<TModel, TParent>
 */
class AssociateUser extends AssociateAction
{
    /**
     * Get the relation name, must be a belongs-to relationship.
     */
    public function relationship(): string
    {
        return 'user';
    }
}
