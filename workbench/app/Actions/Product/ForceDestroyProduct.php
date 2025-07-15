<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\ForceDestroyAction;
use Workbench\App\Models\Product;

/**
 * @template TModel of \Workbench\App\Models\Product
 *
 * @extends \Honed\Action\Actions\ForceDestroyAction<TModel>
 */
class ForceDestroyProduct extends ForceDestroyAction
{
    /**
     * {@inheritdoc}
     */
    public function from(): string
    {
        return Product::class;
    }
}
