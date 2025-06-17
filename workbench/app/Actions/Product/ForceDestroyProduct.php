<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Presets\ForceDestroyAction;

/**
 * @template TModel of \Workbench\App\Models\Product
 *
 * @extends \Honed\Action\Presets\ForceDestroyAction<TModel>
 */
class ForceDestroyProduct extends ForceDestroyAction
{
    //
} 