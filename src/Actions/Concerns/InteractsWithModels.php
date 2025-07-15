<?php

declare(strict_types=1);

namespace Honed\Action\Actions\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Traversable;

trait InteractsWithModels
{
    /**
     * Get the key of the model.
     */
    protected function getKey(Model|int|string $model): int|string
    {
        if ($model instanceof Model) {
            /** @var int|string */
            return $model->getKey();
        }

        return $model;
    }

    /**
     * Indicate whether touched columns should be updated.
     */
    protected function touch(): bool
    {
        return true;
    }

    /**
     * Deiterate the value if it is an iterable.
     *
     * @return array<int, mixed>
     */
    protected function arrayable(mixed $value): array
    {
        return match (true) {
            is_array($value) => $value,
            $value instanceof Traversable => iterator_to_array($value),
            default => Arr::wrap($value),
        };
    }
}
