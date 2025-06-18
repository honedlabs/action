<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\ReplicateAction;

/**
 * @template TModel of \Workbench\App\Models\Product
 *
 * @extends \Honed\Action\Actions\ReplicateAction<TModel>
 */
class ReplicateProduct extends ReplicateAction
{
    /**
     * Get the attributes to exclude from the replication.
     *
     * @return array<int, string>
     */
    protected function except()
    {
        return ['price'];
    }
}
