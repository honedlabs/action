<?php

declare(strict_types=1);

namespace Workbench\App\Actions\Product;

use Honed\Action\Actions\DispatchAction;
use Workbench\App\Events\ProductCreated;

/**
 * @template TDispatch of \Workbench\App\Events\ProductCreated
 * @template TPayload of \Workbench\App\Models\Product
 *
 * @extends \Honed\Action\Actions\DispatchAction<TDispatch, TPayload>
 */
class DispatchProductCreated extends DispatchAction
{
    /**
     * Get the dispatchable class.
     */
    public function dispatch(): string
    {
        return ProductCreated::class;
    }
}
