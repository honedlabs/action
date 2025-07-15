<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\DestroyAction;
use Workbench\App\Models\Product;

/**
 * @template TModel of \Workbench\App\Models\Product
 *
 * @extends \Honed\Action\Actions\DestroyAction<TModel>
 */
class DestroyProduct extends DestroyAction
{
    /**
     * {@inheritdoc}
     */
    public function from(): string
    {
        return Product::class;
    }
}
