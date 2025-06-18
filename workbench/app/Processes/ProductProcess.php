<?php

declare(strict_types=1);

namespace Workbench\App\Processes;

use Honed\Action\Process;
use Workbench\App\Actions\Product\DestroyProduct;
use Workbench\App\Actions\Product\StoreProduct;

/**
 * @template TPayload
 * @template TResult
 *
 * @extends Honed\Command\Process<TPayload, TResult>
 */
class ProductCreationProcess extends Process
{
    /**
     * Indicate whether to wrap the callback in a database transaction.
     *
     * @var bool
     */
    protected $transaction = true;

    /**
     * The tasks to be sequentially executed.
     *
     * @return array<int, class-string>
     */
    protected function tasks()
    {
        return [
            StoreProduct::class,
            DestroyProduct::class,
        ];
    }
}
