<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\UpsertAction;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Workbench\App\Models\Product;

/**
 * @template TModel of \Workbench\App\Models\Product
 *
 * @extends \Honed\Action\Actions\UpsertAction<TModel>
 */
class UpsertProduct extends UpsertAction
{
    /**
     * Get the model to perform the upsert on.
     *
     * @return class-string<TModel>
     */
    public function from(): string|Builder
    {
        return Product::class;
    }

    /**
     * Get the unique by columns for the upsert.
     *
     * @return array<int, string>
     */
    public function uniqueBy(): array
    {
        return ['id'];
    }

    /**
     * Get the columns to update in the upsert.
     *
     * @return array<int, string>
     */
    public function update(): array
    {
        return ['name'];
    }
}
