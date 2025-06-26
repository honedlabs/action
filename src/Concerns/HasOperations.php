<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Batch;
use Honed\Action\Operations\InlineOperation;
use Honed\Action\Operations\Operation;

use function array_filter;
use function array_map;
use function array_values;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @phpstan-require-extends \Honed\Core\Primitive
 */
trait HasOperations
{
    /**
     * Whether the operations should be provided.
     *
     * @var bool
     */
    protected $operable = true;

    /**
     * List of the operations.
     *
     * @var array<int,Operation|Batch<TModel, TBuilder>>
     */
    protected $operations = [];

    /**
     * Whether the instance should provide inline operations.
     *
     * @var bool
     */
    protected $inlinable = true;

    /**
     * Whether the instance should provide bulk operations.
     *
     * @var bool
     */
    protected $bulkable = true;

    /**
     * Whether the instance should provide page operations.
     *
     * @var bool
     */
    protected $pageable = true;

    /**
     * Set whether the operations should be provided.
     *
     * @param  bool  $value
     * @return $this
     */
    public function operable($value = true)
    {
        $this->operable = $value;

        return $this;
    }

    /**
     * Set whether the operations should not be provided.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notOperable($value = true)
    {
        return $this->operable(! $value);
    }

    /**
     * Determine if the operations should be provided.
     *
     * @return bool
     */
    public function isOperable()
    {
        return $this->operable;
    }

    /**
     * Determine if the operations should not be provided.
     *
     * @return bool
     */
    public function isNotOperable()
    {
        return ! $this->isOperable();
    }

    /**
     * Merge a set of operations with existing.
     *
     * @param  Operation|Batch|array<int, Operation|Batch<TModel, TBuilder>>  $operations
     * @return $this
     */
    public function operations($operations)
    {
        /** @var array<int, Operation|Batch> */
        $operations = is_array($operations) ? $operations : func_get_args();

        $this->operations = [...$this->operations, ...$operations];

        return $this;
    }

    /**
     * Insert an operation.
     *
     * @param  Operation|Batch  $operation
     * @return $this
     */
    public function operation($operation)
    {
        $this->operations[] = $operation;

        return $this;
    }

    /**
     * Retrieve the operations
     *
     * @return array<int,Operation>
     */
    public function getOperations()
    {
        if ($this->isNotOperable()) {
            return [];
        }

        $operations = [];

        foreach ($this->operations as $operation) {
            if ($operation instanceof Batch) {
                $operations = [...$operations, ...$operation->getOperations()];
            } else {
                $operations[] = $operation;
            }
        }

        return $operations;
    }

    /**
     * Set the inline operations for the instance, or update whether the instance should provide inline operations.
     *
     * @param  Operation|array<int, Operation>|bool  $operations
     * @return $this
     */
    public function inlineOperations($operations)
    {
        return match (true) {
            is_bool($operations) => $this->inlinable($operations),
            default => $this->operations($operations),
        };
    }

    /**
     * Set whether the instance should provide inline operations.
     *
     * @param  bool  $inlinable
     * @return $this
     */
    public function inlinable($inlinable = true)
    {
        $this->inlinable = $inlinable;

        return $this;
    }

    /**
     * Set whether the instance should not provide inline operations.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notInlinable($value = true)
    {
        return $this->inlinable(! $value);
    }

    /**
     * Determine if the instance should provide inline operations.
     *
     * @return bool
     */
    public function isInlinable()
    {
        return $this->inlinable;
    }

    /**
     * Determine if the instance is not providing inline operations.
     *
     * @return bool
     */
    public function isNotInlinable()
    {
        return ! $this->isInlinable();
    }

    /**
     * Retrieve only the allowed inline operations.
     *
     * @return array<int,InlineOperation>
     */
    public function getInlineOperations()
    {
        if ($this->isNotInlinable()) {
            return [];
        }

        /** @var array<int,InlineOperation> */
        return array_values(
            array_filter(
                $this->getOperations(),
                static fn (Operation $operation) => $operation->isInline()
            )
        );
    }

    /**
     * Get the inline operations as an array.
     *
     * @param  array<string,mixed>|\Illuminate\Database\Eloquent\Model|null  $model
     * @return array<int,mixed>
     */
    public function inlineOperationsToArray($model = null)
    {
        return array_map(
            static fn (InlineOperation $operation) => $operation->toArray(),
            array_values(
                array_filter(
                    $this->getInlineOperations(),
                    static fn (InlineOperation $operation) => $operation
                        ->record($model)
                        ->isAllowed()
                )
            )
        );
    }

    /**
     * Set the bulk operations for the instance, or update whether the instance should provide bulk operations.
     *
     * @param  Operation|array<int, Operation>|bool  $operations
     * @return $this
     */
    public function bulkOperations($operations)
    {
        return match (true) {
            is_bool($operations) => $this->bulkable($operations),
            default => $this->operations($operations),
        };
    }

    /**
     * Set whether the instance should provide bulk operations.
     *
     * @param  bool  $bulkable
     * @return $this
     */
    public function bulkable($bulkable = true)
    {
        $this->bulkable = $bulkable;

        return $this;
    }

    /**
     * Set whether the instance should not provide bulk operations.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notBulkable($value = true)
    {
        return $this->bulkable(! $value);
    }

    /**
     * Determine if the instance should provide bulk operations.
     *
     * @return bool
     */
    public function isBulkable()
    {
        return $this->bulkable;
    }

    /**
     * Determine if the instance is not providing bulk operations.
     *
     * @return bool
     */
    public function isNotBulkable()
    {
        return ! $this->isBulkable();
    }

    /**
     * Retrieve only the allowed bulk operations.
     *
     * @return array<int,Operation>
     */
    public function getBulkOperations()
    {
        if ($this->isNotBulkable()) {
            return [];
        }

        return array_values(
            array_filter(
                $this->getOperations(),
                static fn (Operation $operation) => $operation->isBulk() &&
                    $operation->isAllowed()
            )
        );
    }

    /**
     * Get the bulk operations as an array.
     *
     * @return array<int,mixed>
     */
    public function bulkOperationsToArray()
    {
        return array_map(
            static fn (Operation $operation) => $operation->toArray(),
            $this->getBulkOperations()
        );
    }

    /**
     * Set the page operations for the instance, or update whether the instance should provide page operations.
     *
     * @param  Operation|array<int, Operation>|bool  $operations
     * @return $this
     */
    public function pageOperations($operations)
    {
        return match (true) {
            is_bool($operations) => $this->pageable($operations),
            default => $this->operations($operations),
        };
    }

    /**
     * Set whether the instance should provide page operations.
     *
     * @param  bool  $pageable
     * @return $this
     */
    public function pageable($pageable = true)
    {
        $this->pageable = $pageable;

        return $this;
    }

    /**
     * Set whether the instance should not provide page operations.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notPageable($value = true)
    {
        return $this->pageable(! $value);
    }

    /**
     * Determine if the instance should provide page operations.
     *
     * @return bool
     */
    public function isPageable()
    {
        return $this->pageable;
    }

    /**
     * Determine if the instance is not providing page operations.
     *
     * @return bool
     */
    public function isNotPageable()
    {
        return ! $this->isPageable();
    }

    /**
     * Retrieve only the allowed page operations.
     *
     * @return array<int,Operation>
     */
    public function getPageOperations()
    {
        if ($this->isNotPageable()) {
            return [];
        }

        return array_values(
            array_filter(
                $this->getOperations(),
                static fn (Operation $operation) => $operation->isPage() &&
                    $operation->isAllowed()
            )
        );
    }

    /**
     * Get the page operations as an array.
     *
     * @return array<int,mixed>
     */
    public function pageOperationsToArray()
    {
        return array_map(
            static fn (Operation $operation) => $operation->toArray(),
            $this->getPageOperations()
        );
    }
}
