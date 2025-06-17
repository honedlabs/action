<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Presets\DissociateAction;

/**
 * @template TModel of \Workbench\App\Models\User
 * @template TParent of \Workbench\App\Models\Product
 *
 * @extends \Honed\Action\Presets\DissociateAction<TModel, TParent>
 */
class DissociateUser extends DissociateAction
{
    /**
     * Get the relation name, must be a belongs to relationship.
     *
     * @return string
     */
    protected function relationship()
    {
        return 'product';
    }
} 