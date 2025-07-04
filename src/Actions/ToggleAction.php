<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Contracts\Relatable;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TToggle of \Illuminate\Database\Eloquent\Model
 * @template TInput of mixed = array<string, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 */
abstract class ToggleAction extends DatabaseAction implements Relatable
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use Concerns\InteractsWithFormData;

    use Concerns\InteractsWithModels;

    /**
     * Toggle the models in the relationship.
     *
     * @param  TModel  $model
     * @param  int|string|TToggle|array<int, int|string|TToggle>  $toggles
     * @param  TInput  $attributes
     * @return TModel
     */
    public function handle($model, $toggles, $attributes = [])
    {
        $this->transact(
            fn () => $this->toggle($model, $toggles, $attributes)
        );

        return $model;
    }

    /**
     * Get the relation for the model.
     *
     * @param  TModel  $model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TModel, TToggle>
     */
    protected function getRelation($model)
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsToMany<TModel, TToggle> */
        return $model->{$this->relationship()}();
    }

    /**
     * Prepare the models and attributes for the toggle method.
     *
     * @param  int|string|TToggle|array<int, int|string|TToggle>  $toggles
     * @param  TInput  $attributes
     * @return array<int|string, array<string, mixed>>
     */
    protected function prepare($toggles, $attributes)
    {
        /** @var array<int, int|string|TToggle> */
        $toggles = $this->arrayable($toggles);

        $attributes = $this->normalize($attributes);

        return Arr::mapWithKeys(
            $toggles,
            fn ($togglement) => [
                $this->getKey($togglement) => $attributes,
            ]
        );
    }

    /**
     * Toggle the models in the relationship.
     *
     * @param  TModel  $model
     * @param  int|string|TToggle|array<int, int|string|TToggle>  $toggles
     * @param  TInput  $attributes
     * @return void
     */
    protected function toggle($model, $toggles, $attributes)
    {
        $toggling = $this->prepare($toggles, $attributes);

        $toggled = $this->getRelation($model)->toggle($toggling, $this->shouldTouch());

        $this->after($model, $toggled);
    }

    /**
     * Perform additional logic after the model has been toggleed.
     *
     * @param  TModel  $model
     * @param  array<mixed>  $toggled
     * @return void
     */
    protected function after($model, $toggled)
    {
        //
    }
}
