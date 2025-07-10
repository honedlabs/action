<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\TouchAction;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends TouchAction<Product>
 */
class TouchProduct extends TouchAction
{
    /**
     * Get the column(s) to touch. By default, it will update the updated at
     * at columns. You can select a single column, multiple columns by passing
     * the names. Or, you can pass `true` to touch the owner relationships.
     *
     * @return array<int, string>
     */
    public function touches(): array
    {
        return ['updated_at', 'created_at'];
    }
}
