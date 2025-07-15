<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\BulkAssociateAction;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Workbench\App\Models\Product;

/**
 * @template TModel of \Workbench\App\Models\Product
 * @template TParent of \Workbench\App\Models\User
 * @template TAction of \Workbench\App\Actions\Product\AssociateUser
 *
 * @extends \Honed\Action\Actions\BulkAssociateAction<TModel, TParent, TAction>
 */
class BulkAssociateUser extends BulkAssociateAction
{
    /**
     * {@inheritdoc}
     */
    public function from(): string|Builder
    {
        return Product::class;
    }

    /**
     * {@inheritdoc}
     */
    public function action(): string
    {
        return AssociateUser::class;
    }
}
