<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\BulkAttachAction;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Workbench\App\Models\Product;

/**
 * @template TModel of \Workbench\App\Models\Product
 * @template TUser of \Workbench\App\Models\User
 * @template TAction of \Workbench\App\Actions\Product\AttachUser
 *
 * @extends \Honed\Action\Actions\BulkAttachAction<TModel, TUser, TAction>
 */
class BulkAttachUsers extends BulkAttachAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): string
    {
        return AttachUser::class;
    }

    /**
     * {@inheritdoc}
     */
    public function from(): string|Builder
    {
        return Product::class;
    }
}
