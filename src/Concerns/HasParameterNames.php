<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
    public static function getParameterNames($parameter)
    {
        $model = $parameter instanceof Builder
            ? $parameter->getModel()
            : $parameter;

        /** @var \Illuminate\Support\Stringable $strTable */
        $strTable = str($model->getTable());

        return [
            $model,
            $strTable->singular()->camel()->toString(),
            $strTable->camel()->toString(),
        ];
    }

    /**
     * Retrieve the named and typed parameters for the action.
     *
     * @template T of \Illuminate\Database\Eloquent\Model
     *
     * @param  T|\Illuminate\Database\Eloquent\Builder<T>  $query
     * @return array{non-empty-array<string, T|\Illuminate\Database\Eloquent\Builder<T>>, non-empty-array<class-string, T|\Illuminate\Database\Eloquent\Builder<T>>}
     */
    public static function getNamedAndTypedParameters($query)
    {
        [$model, $singular, $plural] = static::getParameterNames($query);

        $named = \array_fill_keys(
            ['model', 'record', 'query', 'builder', $singular, $plural],
            $query
        );

        $typed = [
            Model::class => $query,
            Builder::class => $query,
            $model::class => $query,
        ];

        return [$named, $typed];
    }
}
