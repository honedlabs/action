<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

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
    public function handle($model, $input)
    {
        return $this->transact(
            fn () => $this->update($model, $input)
        );
    }

    /**
     * Prepare the input for the update method.
     *
     * @param  TInput  $input
     * @param  TModel  $model
     * @return array<string, mixed>
     */
    protected function prepare($model, $input)
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
    protected function update($model, $input)
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
     * @return void
     */
    protected function after($model, $input, $prepared)
    {
        //
    }
}
