<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TInput of mixed = array<string, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 */
class UpdateAction extends DatabaseAction
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use Concerns\InteractsWithFormData;

    /**
     * Update the provided model using the input.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @return TModel $model
     */
    public function handle(Model $model, $input): Model
    {
        return $this->transact(
            fn () => $this->update($model, $input)
        );
    }

    /**
     * Prepare the input for the update method.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @return array<string, mixed>
     */
    protected function prepare(Model $model, $input): array
    {
        return $this->only(
            $this->normalize($input)
        );
    }

    /**
     * Update the record in the database.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @return TModel
     */
    protected function update(Model $model, $input): Model
    {
        $prepared = $this->prepare($model, $input);

        $model->update($prepared);

        $this->after($model, $input, $prepared);

        return $model;
    }

    /**
     * Perform additional database transactions after the model has been updated.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @param  array<string, mixed>  $prepared
     */
    protected function after(Model $model, $input, array $prepared): void
    {
        //
    }
}
