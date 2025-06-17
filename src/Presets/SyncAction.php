<?php

declare(strict_types=1);

namespace Honed\Action\Presets;

use Honed\Action\Contracts\Actionable;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TSync of \Illuminate\Database\Eloquent\Model
 */
abstract class SyncAction implements Actionable
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
     * Sync models to the relationship.
     *
     * @param  TModel  $model
     * @param  int|string|TSync|array<int, int|string|TSync>  $syncs
     * @param  array<string, mixed>  $attributes
     * @return void
     */
    public function handle($model, $syncs, $attributes = [])
    {
        $this->transact(
            fn () => $this->sync($model, $syncs, $attributes)
        );
    }

    /**
     * Get the relation for the model.
     *
     * @param  TModel  $model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TModel, TSync>
     */
    protected function getRelation($model)
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsToMany<TModel, TSync> */
        return $model->{$this->relationship()}();
    }

    /**
     * Prepare the sync data and attributes for the sync method.
     *
     * @param  int|string|TSync|array<int, int|string|TSync>  $syncs
     * @param  array<string, mixed>  $attributes
     * @return array<int|string, array<string, mixed>>
     */
    protected function prepare($syncs, $attributes)
    {
        $syncs = is_array($syncs) ? $syncs : [$syncs];

        return Arr::mapWithKeys(
            $syncs,
            fn ($syncment) => [
                $this->getKey($syncment) => $attributes,
            ]
        );
    }

    /**
     * Get the values to insert into the pivot table.
     *
     * @return array<string, mixed>
     */
    protected function pivot()
    {
        return [];
    }

    /**
     * Sync the relationship in the database.
     *
     * @param  TModel  $model
     * @param  int|string|TSync|array<int, int|string|TSync>  $syncs
     * @param  array<string, mixed>  $attributes
     * @return void
     */
    protected function sync($model, $syncs, $attributes)
    {
        $syncing = $this->prepare($syncs, $attributes);

        $synced = filled($pivot = $this->pivot())
            ? $this->getRelation($model)->syncWithPivotValues($syncing, $pivot, $this->shouldDetach())
            : $this->getRelation($model)->sync($syncing, $this->shouldDetach());

        $this->after($model, $synced['attached'], $synced['detached'], $synced['updated']);
    }

    /**
     * Perform additional logic after the model has been synced.
     *
     * @param  TModel  $model
     * @param  array<int, int|string>  $attached
     * @param  array<int, int|string>  $detached
     * @param  array<int, int|string>  $updated
     * @return void
     */
    protected function after($model, $attached, $detached, $updated)
    {
        //
    }

    /**
     * Indicate whether the relationship sync should detach records.
     *
     * @return bool
     */
    protected function shouldDetach()
    {
        return true;
    }
}
