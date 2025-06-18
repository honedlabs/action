<?php

declare(strict_types=1);

namespace Honed\Action\Actions\Concerns;

use Illuminate\Database\Eloquent\Model;
use Traversable;

trait InteractsWithModels
{
    /**
     * Get the key of the model.
     *
     * @param  Model|int|string  $model
     * @return int|string
     */
    protected function getKey($model)
    {
        if ($model instanceof Model) {
            /** @var int|string */
            return $model->getKey();
        }

        return $model;
    }

    /**
     * Indicate whether touched columns should be updated.
     *
     * @return bool
     */
    protected function shouldTouch()
    {
        return true;
    }

    /**
     * Deiterate the value if it is an iterable.
     *
     * @param  mixed  $value
     * @return array<int, mixed>
     */
    protected function arrayable($value)
    {
        return match (true) {
            is_array($value) => $value,
            $value instanceof Traversable => iterator_to_array($value),
            default => [$value],
        };
    }
}
