<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Presets\UpsertAction;

/**
 * @template TModel of \Workbench\App\Models\Product
 *
 * @extends \Honed\Action\Presets\UpsertAction<TModel>
 */
class UpsertProduct extends UpsertAction
{
    /**
     * Get the model to perform the upsert on.
     *
     * @return class-string<TModel>
     */
    protected function for()
    {
        return \Workbench\App\Models\Product::class;
    }

    /**
     * Get the unique by columns for the upsert.
     *
     * @return array<int, string>
     */
    protected function uniqueBy()
    {
        return ['name'];
    }

    /**
     * Get the columns to update in the upsert.
     *
     * @return array<int, string>
     */
    protected function update()
    {
        return ['description', 'price'];
    }
} 