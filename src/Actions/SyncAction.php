<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Contracts\Relatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TSync of \Illuminate\Database\Eloquent\Model
 */
abstract class SyncAction extends DatabaseAction implements Relatable
{
    use Concerns\InteractsWithModels;

    /**
     * Sync models to the relationship.
     *
     * @param  TModel  $model
     * @param  int|string|TSync|array<int, int|string|TSync>  $syncs
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    public function handle(Model $model, $syncs, array $attributes = []): Model
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
     * @return BelongsToMany<TModel, TSync>
     */
    protected function getRelation(Model $model): BelongsToMany
    {
        /** @var BelongsToMany<TModel, TSync> */
        return $model->{$this->relationship()}();
    }

    /**
     * Prepare the sync data and attributes for the sync method.
     *
     * @param  int|string|TSync|array<int, int|string|TSync>  $syncs
     * @param  array<string, mixed>  $attributes
     * @return array<int|string, array<string, mixed>>
     */
    protected function prepare($syncs, array $attributes): array
    {
        /** @var array<int, int|string|TSync> */
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
    protected function pivot(): array
    {
        return [];
    }

    /**
     * Sync the relationship in the database.
     *
     * @param  TModel  $model
     * @param  int|string|TSync|array<int, int|string|TSync>  $syncs
     * @param  array<string, mixed>  $attributes
     */
    protected function sync(Model $model, $syncs, array $attributes): void
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
    protected function after(Model $model, array $attached, array $detached, array $updated)
    {
        //
    }

    /**
     * Indicate whether the relationship sync should detach records.
     */
    protected function shouldDetach(): bool
    {
        return true;
    }
}
