<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Presets\StoreAction;
use Workbench\App\Models\Product;

/**
 * @template TModel of \Workbench\App\Models\Product
 *
 * @extends \Honed\Action\Presets\StoreAction<TModel>
 */
class StoreProduct extends StoreAction
{
    /**
     * Get the model to store the input data in.
     *
     * @return class-string<TModel>
     */
    protected function for()
    {
        return Product::class;
    }
} 