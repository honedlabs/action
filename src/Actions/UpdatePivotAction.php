<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithFormData;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TPivot of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Relations\Pivot
 * @template TInput of mixed = array<string, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 */
class UpdatePivotAction extends DatabaseAction
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use InteractsWithFormData;

    /**
     * Update the pivot model.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @return TModel
     */
    public function handle(Model $model, $input = []): Model
    {
        return $this->transaction(
            fn () => $this->execute($model, $input)
        );
    }

    /**
     * Execute the action.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @return TModel
     */
    public function execute(Model $model, $input): Model
    {
        $pivot = $this->pivot($model);

        $attributes = $this->attributes($model, $pivot, $input);

        $pivot->update($attributes);

        $this->after($model, $pivot, $input, $attributes);

        return $model;
    }

    /**
     * Prepare the input for the update method.
     *
     * @param  TModel  $model
     * @param  TPivot  $pivot
     * @param  TInput  $input
     * @return array<string, mixed>
     */
    public function attributes(Model $model, Model $pivot, $input): array
    {
        return $this->normalize($input);
    }

    /**
     * Get the pivot model to update.
     *
     * @param  TModel  $model
     * @return TPivot
     */
    public function pivot(Model $model): Model
    {
        /** @var TPivot */
        return $model->pivot; // @phpstan-ignore-line property.notFound
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TModel  $model
     * @param  TPivot  $pivot
     * @param  TInput  $input
     * @param  array<string, mixed>  $prepared
     */
    public function after(Model $model, Model $pivot, $input, array $prepared): void
    {
        //
    }
}
