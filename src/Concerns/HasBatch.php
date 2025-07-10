<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Honed\Action\Attributes\UseBatch;
use Honed\Action\Batch;
use ReflectionClass;

/**
 * @template TBatch of \Honed\Action\Batch = \Honed\Action\Batch
 *
 * @property class-string<TBatch> $batch
 */
trait HasBatch
{
    /**
     * Get the action group instance for the model.
     *
     * @return TBatch
     */
    public static function batch(): Batch
    {
        return static::newBatch()
            ?? Batch::batchForModel(static::class);
    }

    /**
     * Create a new action group instance for the model.
     *
     * @return TBatch
     *
     * @internal
     */
    protected static function newBatch(): ?Batch
    {
        if (isset(static::$batch)) {
            return static::$batch::make();
        }

        if ($batch = static::getUseBatchAttribute()) {
            return $batch::make();
        }

        return null;
    }

    /**
     * Get the actions from the UseBatch class attribute.
     *
     * @return class-string<TBatch>|null
     *
     * @internal
     */
    protected static function getUseBatchAttribute(): ?string
    {
        $attributes = (new ReflectionClass(static::class))
            ->getAttributes(UseBatch::class);

        if ($attributes !== []) {
            $group = $attributes[0]->newInstance();

            return $group->batchClass;
        }

        return null;
    }
}
