<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\StoreAction;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Workbench\App\Models\Product;

/**
 * @template TModel of \Workbench\App\Models\Product
 *
 * @extends \Honed\Action\Actions\StoreAction<TModel>
 */
class StoreProduct extends StoreAction
{
    /**
     * Get the model to store the input data in.
     *
     * @return class-string<TModel>
     */
    public function from(): string|Builder
    {
        return Product::class;
    }
}
