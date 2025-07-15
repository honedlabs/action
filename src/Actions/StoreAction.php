<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithFormData;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TInput of mixed = array<string, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 *
 * @extends \Honed\Action\Actions\EloquentAction<TModel>
 */
abstract class StoreAction extends EloquentAction
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use InteractsWithFormData;

    /**
     * Store the input data in the database.
     *
     * @param  TInput  $input
     * @return TModel $model
     */
    public function handle($input): Model
    {
        return $this->transaction(
            fn () => $this->execute($input)
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
     * Execute the action.
     *
     * @param  TInput  $input
     * @return TModel
     */
    protected function execute($input): Model
    {
        $prepared = $this->prepare($input);

        /** @var TModel */
        $model = $this->query()->create($prepared);

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
