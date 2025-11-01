<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithFormData;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TInput of mixed = array<string, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 */
abstract class ModelAction extends DatabaseAction
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use InteractsWithFormData;

    /**
     * Act on the model.
     *
     * @param  TModel  $model
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    abstract public function act(Model $model, array $attributes): Model;

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
     * Prepare the input for the action.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @return array<string, mixed>
     */
    public function attributes(Model $model, $input): array
    {
        return $this->normalize($input);
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
        $this->before($model, $input);

        $attributes = $this->attributes($model, $input);

        $model = $this->act($model, $attributes);

        $this->after($model, $input, $attributes);

        return $model;
    }

    /**
     * Perform additional logic before the action has been executed.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     */
    public function before(Model $model, $input): void {}

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @param  array<string, mixed>  $attributes
     */
    public function after(Model $model, $input, array $attributes): void {}
}
