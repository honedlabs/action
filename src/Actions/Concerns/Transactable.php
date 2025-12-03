<?php

declare(strict_types=1);

namespace Honed\Action\Actions\Concerns;

use Closure;
use Honed\Action\Attributes\Transact;
use Illuminate\Support\Facades\DB;
use ReflectionClass;

trait Transactable
{
    /**
     * Indicate whether to wrap the callback in a database transaction.
     *
     * @var bool|null
     */
    protected $transact;

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
        return $this->transact
            ??= static::hasTransactionAttribute() ?: static::defaultTransact();
    }

    /**
     * Determine whether to not wrap the operation in a database transaction.
     */
    public function isNotTransaction(): bool
    {
        return ! $this->isTransaction();
    }

    /**
     * Perform a database operation.
     *
     * @template TReturn of mixed
     *
     * @param  Closure():TReturn  $callback
     * @return TReturn
     */
    public function transaction(Closure $callback): mixed
    {
        if ($this->isTransaction()) {
            return DB::transaction($callback);
        }

        return $callback();
    }

    /**
     * Determine whether the transactions should be enabled by default.
     */
    protected static function defaultTransact(): bool
    {
        return (bool) config()->boolean('action.transact', false);
    }

    /**
     * Get the form from the Form class attribute.
     */
    protected static function hasTransactionAttribute(): bool
    {
        return filled(
            (new ReflectionClass(static::class))->getAttributes(Transact::class)
        );
    }
}
