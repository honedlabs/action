<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait HasParameterNames
{
    /**
     * Retrieve the parameter names for the action.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  T|\Illuminate\Database\Eloquent\Builder<T>  $parameter
     * @return array{0: T, 1: string, 2: string}
     */
    public function getParameterNames(Builder|Model $parameter): array
    {
        $model = $parameter instanceof Builder
            ? $parameter->getModel()
            : $parameter;

        $table = $model->getTable();

        return [
            $model,
            str($table)->singular()->camel()->toString(),
            str($table)->camel()->toString(),
        ];
    }
}