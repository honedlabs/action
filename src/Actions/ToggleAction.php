<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithFormData;
use Honed\Action\Actions\Concerns\InteractsWithModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TToggle of \Illuminate\Database\Eloquent\Model
 * @template TInput of mixed = array<string, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 *
 * @extends \Honed\Action\Actions\BelongsToManyAction<TModel, TToggle>
 */
abstract class ToggleAction extends BelongsToManyAction
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use InteractsWithFormData;

    use InteractsWithModels;

    /**
     * Toggle the models in the relationship.
     *
     * @template T of int|string|TToggle|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $ids
     * @param  TInput  $attributes
     * @return TModel
     */
    public function handle(Model $model, $ids, $attributes = []): Model
    {
        $this->transaction(
            fn () => $this->execute($model, $ids, $attributes)
        );

        return $model;
    }

    /**
     * Prepare the models and attributes for the toggle method.
     *
     * @template T of int|string|TToggle|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $ids
     * @param  TInput  $attributes
     * @return array<int|string, array<string, mixed>>
     */
    protected function prepare($ids, $attributes): array
    {
        /** @var array<int, int|string|TToggle> */
        $ids = $this->arrayable($ids);

        $attributes = $this->normalize($attributes);

        return Arr::mapWithKeys(
            $ids,
            fn ($id) => [
                $this->getKey($id) => $attributes,
            ]
        );
    }

    /**
     * Execute the action.
     *
     * @template T of int|string|TToggle|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $ids
     * @param  TInput  $attributes
     */
    protected function execute(Model $model, $ids, $attributes): void
    {
        $toggling = $this->prepare($ids, $attributes);

        /** @var array{attached: array<int, int|string>, detached: array<int, int|string>} */
        $toggled = $this->getRelationship($model)->toggle($toggling, $this->touch());

        $this->after($model, $toggled['attached'], $toggled['detached']);
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TModel  $model
     * @param  array<int, int|string>  $attached
     * @param  array<int, int|string>  $detached
     */
    protected function after(Model $model, array $attached, array $detached): void
    {
        //
    }
}
