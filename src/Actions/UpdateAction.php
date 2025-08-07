<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithFormData;
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
    use InteractsWithFormData;

    /**
     * Update the model.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @return TModel $model
     */
    public function handle(Model $model, $input = []): Model
    {
        return $this->transaction(
            fn () => $this->execute($model, $input)
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
     * Execute the action.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @return TModel
     */
    protected function execute(Model $model, $input): Model
    {
        $prepared = $this->prepare($model, $input);

        $model->update($prepared);

        $this->after($model, $input, $prepared);

        return $model;
    }

    /**
     * Perform additional logic after the action has been executed.
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
