<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\BulkDetachAction;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Workbench\App\Models\Product;

/**
 * @template TModel of \Workbench\App\Models\Product
 * @template TUser of \Workbench\App\Models\User
 * @template TAction of \Workbench\App\Actions\Product\DetachUser
 *
 * @extends \Honed\Action\Actions\BulkDetachAction<TModel, TUser, TAction>
 */
class BulkDetachUsers extends BulkDetachAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): string
    {
        return DetachUser::class;
    }

    /**
     * {@inheritdoc}
     */
    public function from(): string|Builder
    {
        return Product::query();
    }
}
