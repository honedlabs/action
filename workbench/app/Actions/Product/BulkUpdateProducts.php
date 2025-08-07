<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\BulkUpdateAction;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Workbench\App\Models\Product;

/**
 * @template TModel of \Workbench\App\Models\Product
 * @template TInput = []
 *
 * @extends \Honed\Action\Actions\BulkUpdateAction<TModel, TInput>
 */
class BulkUpdateProducts extends BulkUpdateAction
{
    /**
     * {@inheritdoc}
     */
    public function from(): string|Builder
    {
        return Product::class;
    }
}
