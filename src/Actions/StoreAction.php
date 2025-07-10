<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithFormData;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TInput of mixed = array<string, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 */
abstract class StoreAction extends DatabaseAction
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use InteractsWithFormData;

    /**
     * Get the model to store the input data in.
     *
     * @return class-string<TModel>
     */
    abstract protected function for(): string;

    /**
     * Store the input data in the database.
     *
     * @param  TInput  $input
     * @return TModel $model
     */
    public function handle($input): Model
    {
        return $this->transact(
            fn () => $this->store($input)
        );
    }

    /**
     * Prepare the input for the update method.
     *
     * @param  TInput  $input
     * @return array<string, mixed>
     */
    protected function prepare($input): array
    {
        return $this->only(
            $this->normalize($input)
        );
    }

    /**
     * Store the record in the database.
     *
     * @param  TInput  $input
     * @return TModel
     */
    protected function store($input): Model
    {
        $prepared = $this->prepare($input);

        $class = $this->for();

        $model = (new $class())->query()->create($prepared);

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
    protected function after(Model $model, $input, $prepared): void
    {
        //
    }
}
