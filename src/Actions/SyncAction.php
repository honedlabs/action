<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Concerns\CanBeTransaction;
use Honed\Action\Contracts\Action;
use Honed\Action\Contracts\Relatable;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TSync of \Illuminate\Database\Eloquent\Model
 */
abstract class SyncAction implements Action, Relatable
{
    use CanBeTransaction;
    use Concerns\InteractsWithModels;

    /**
     * Sync models to the relationship.
     *
     * @param  TModel  $model
     * @param  int|string|TSync|array<int, int|string|TSync>  $syncs
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    public function handle($model, $syncs, $attributes = [])
    {
        $this->transact(
            fn () => $this->sync($model, $syncs, $attributes)
        );

        return $model;
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
        $syncs = $this->arrayable($syncs);

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
