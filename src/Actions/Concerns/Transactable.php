<?php

declare(strict_types=1);

namespace Honed\Action\Actions\Concerns;

use Closure;
use Illuminate\Support\Facades\DB;

trait Transactable
{
    /**
     * Indicate whether to wrap the callback in a database transaction.
     *
     * @var bool|null
     */
    protected $transact;

    /**
     * Indicate whether all actions of this type should be wrapped in a transaction.
     *
     * @var bool
     */
    protected static $transaction = false;

    /**
     * Set whether to wrap the callback in a database transaction.
     */
    public static function withinTransaction(bool $value = true): void
    {
        static::$transaction = $value;
    }

    /**
     * Set whether to not wrap the callback in a database transaction.
     */
    public static function outsideTransaction(bool $value = true): void
    {
        static::withinTransaction(! $value);
    }

    /**
     * Set whether to wrap the callback in a database transaction.
     *
     * @return $this
     */
    public function transact(bool $value = true): static
    {
        $this->transact = $value;

        return $this;
    }

    /**
     * Set whether to not wrap the callback in a database transaction.
     *
     * @return $this
     */
    public function dontTransact(bool $value = true): static
    {
        return $this->transact(! $value);
    }

    /**
     * Determine whether to wrap the operation in a database transaction.
     */
    public function isTransaction(): bool
    {
        return $this->transact ?? static::$transaction;
    }

    /**
     * Determine whether to not wrap the operation in a database transaction.
     */
    public function isNotTransaction(): bool
    {
        return ! $this->isTransaction();
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
    protected function transaction(Closure $callback): mixed
    {
        if ($this->isTransaction()) {
            return DB::transaction($callback);
        }

        return $callback();
    }
}
