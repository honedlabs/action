<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\ForceDeleteAction;

/**
 * @template TModel of \Workbench\App\Models\Product
 *
 * @extends \Honed\Action\Actions\ForceDeleteAction<TModel>
 */
class ForceDeleteProduct extends ForceDeleteAction
{
    //
}
