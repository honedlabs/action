<?php

declare(strict_types=1);

namespace Honed\Action\Presets\Concerns;

use Closure;
use Illuminate\Support\Facades\DB;

trait CanBeTransaction
{
    /**
     * Determine whether to wrap the update in a database transaction.
     *
     * @return bool
     */
    protected function isTransaction()
    {
        return true;
    }

    /**
     * Perform a database operation, possibly wrapped in a transaction and
     * return the result.
     *
     * @template TReturn of mixed
     *
     * @param  Closure():TReturn  $callback
     * @return TReturn
     */
    protected function transact($callback)
    {
        if ($this->isTransaction()) {
            return DB::transaction($callback);
        }

        return $callback();
    }
}
