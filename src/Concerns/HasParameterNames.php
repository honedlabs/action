<?php

declare(strict_types=1);

namespace Honed\Action\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasParameterNames
{
    /**
     * Retrieve the parameter names for the action.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $model
     * @return array{0: class-string<\Illuminate\Database\Eloquent\Model>, 1: string, 2: string}
     */
    public static function getParameterNames($model)
    {
        $model = (match (true) {
            $model instanceof Builder => $model->getModel()::class,
            $model instanceof Model => $model::class,
            default => $model,
        });

        return [
            $model,
            static::getSingularName($model),
            static::getPluralName($model),
        ];
    }

    /**
     * Get the singular name of a model.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @return string
     */
    public static function getSingularName($model)
    {
        return str($model)
            ->classBasename()
            ->singular()
            ->camel()
            ->toString();
    }

    /**
     * Get the plural name of a model.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @return string
     */
    public static function getPluralName($model)
    {
        return Str::of($model)
            ->classBasename()
            ->plural()
            ->camel()
            ->toString();
    }

    /**
     * Retrieve the named and typed parameters for a builder.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $model
     * @param  mixed  $value
     * @return array{array<string, mixed>, array<class-string,mixed>}
     */
    public static function getBuilderParameters($model, $value = null)
    {
        $value ??= $model;

        [$model, $singular, $plural] = static::getParameterNames($model);

        $named = \array_fill_keys(
            ['model', 'record', 'query', 'builder', 'records', $singular, $plural],
            $value
        );

        $typed = \array_fill_keys(
            [Model::class, Builder::class, $model],
            $value
        );

        return [$named, $typed];
    }

    /**
     * Retrieve the named and typed parameters for a model.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $model
     * @param  mixed  $value
     * @return array{array<string, mixed>, array<class-string,mixed>}
     */
    public static function getModelParameters($model, $value = null)
    {
        $value ??= $model;

        [$model, $singular] = static::getParameterNames($model);

        $named = \array_fill_keys(
            ['model', 'record', $singular],
            $value
        );

        $typed = \array_fill_keys(
            [Model::class, $model],
            $value
        );

        return [$named, $typed];
    }
}
