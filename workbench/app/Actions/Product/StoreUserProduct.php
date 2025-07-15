<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\StoreChildAction;

/**
 * @template TParent of \Workbench\App\Models\User
 * @template TNew of \Workbench\App\Models\Product
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\HasMany<TNew, TParent>
 *
 * @extends \Honed\Action\Actions\StoreChildAction<TParent, TNew, TRelation>
 */
class StoreUserProduct extends StoreChildAction
{
    /**
     * Get the model to store the input data in.
     *
     * @return class-string<TParent>
     */
    public function relationship(): string
    {
        return 'products';
    }
}
