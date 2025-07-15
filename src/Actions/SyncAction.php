<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TSync of \Illuminate\Database\Eloquent\Model
 *
 * @extends \Honed\Action\Actions\BelongsToManyAction<TModel, TSync>
 */
abstract class SyncAction extends BelongsToManyAction
{
    use InteractsWithModels;

    /**
     * Sync models to the relationship.
     *
     * @template T of int|string|TSync|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $syncs
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    public function handle(Model $model, $syncs, array $attributes = []): Model
    {
        $this->transaction(
            fn () => $this->execute($model, $syncs, $attributes)
        );

        return $model;
    }

    /**
     * Prepare the sync data and attributes for the sync method.
     *
     * @template T of int|string|TSync|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $syncs
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
     * Execute the action.
     *
     * @template T of int|string|TSync|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $syncs
     * @param  array<string, mixed>  $attributes
     */
    protected function execute(Model $model, $syncs, array $attributes): void
    {
        $syncing = $this->prepare($syncs, $attributes);

        $pivot = $this->pivot();

        $synced = match (true) {
            filled($pivot) => $this->getRelationship($model)->syncWithPivotValues($syncing, $pivot, $this->detach()),
            default => $this->getRelationship($model)->sync($syncing, $this->detach()),
        };

        $this->after($model, $synced['attached'], $synced['detached'], $synced['updated']);
    }

    /**
     * Perform additional logic after the action has been executed.
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
     * Get the values to insert into the pivot table.
     *
     * @return array<string, mixed>
     */
    protected function pivot(): array
    {
        return [];
    }

    /**
     * Indicate whether the relationship sync should detach records.
     */
    protected function detach(): bool
    {
        return true;
    }
}
