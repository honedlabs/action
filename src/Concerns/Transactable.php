<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Closure;
use Illuminate\Support\Facades\DB;

trait Transactable
{
    /**
     * Indicate whether to wrap the callback in a database transaction.
     *
     * @var bool
     */
    protected static $transaction = false;

    /**
     * Set whether to wrap the callback in a database transaction.
     *
     * @param  bool  $transaction
     * @return void
     */
    public static function shouldBeTransaction($transaction = true)
    {
        static::$transaction = $transaction;
    }

    /**
     * Set whether to wrap the callback in a database transaction.
     *
     * @param  bool  $transaction
     * @return $this
     */
    public function transaction($transaction = true)
    {
        static::$transaction = $transaction;

        return $this;
    }

    /**
     * Determine whether to wrap the operation in a database transaction.
     *
     * @return bool
     */
    public function isTransaction()
    {
        return static::$transaction;
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
