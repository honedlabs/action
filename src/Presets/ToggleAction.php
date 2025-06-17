<?php

declare(strict_types=1);

namespace Honed\Action\Presets;

use Honed\Action\Contracts\Actionable;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TToggle of \Illuminate\Database\Eloquent\Model
 */
abstract class ToggleAction implements Actionable
{
    use Concerns\CanBeTransaction;
    use Concerns\InteractsWithModels;

    /**
     * Get the relation name, must be a belongs-to-many relationship.
     *
     * @return string
     */
    abstract protected function relationship();

    /**
     * Toggle the models in the relationship.
     *
     * @param  TModel  $model
     * @param  int|string|TToggle|array<int, int|string|TToggle>  $toggles
     * @param  array<string, mixed>  $attributes
     * @return void
     */
    public function handle($model, $toggles, $attributes = [])
    {
        $this->transact(
            fn () => $this->toggle($model, $toggles, $attributes)
        );
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
     * @param  array<string, mixed>  $attributes
     * @return array<int|string, array<string, mixed>>
     */
    protected function prepare($toggles, $attributes)
    {
        $toggles = is_array($toggles) ? $toggles : [$toggles];

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
     * @param  array<string, mixed>  $attributes
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
     * @param  array<int, int|string>  $toggled
     * @return void
     */
    protected function after($model, $toggled)
    {
        //
    }
}
