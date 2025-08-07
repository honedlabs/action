<?php

declare(strict_types=1);

namespace Honed\Action\Actions\Concerns;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @phpstan-require-extends \Honed\Action\Actions\EloquentAction<TModel>
 */
trait InteractsWithBuilder
{
    /**
     * Get the query for the models.
     *
     * @template T of int|string|null
     *
     * @param  T|array<int, T>|Collection<int, T>|EloquentCollection<int, TModel>  $models
     * @return \Illuminate\Database\Eloquent\Builder<TModel>|\Illuminate\Database\Eloquent\Relations\Relation<TModel, *, *>
     */
    protected function getQuery($models): Builder
    {
        $query = $this->query();

        return $query->whereIn(
            $this->getKey($query),
            $this->getPrimaryKeys($models)
        );
    }

    /**
     * Get the primary keys to scope the query by.
     *
     * @template T of int|string|null
     *
     * @param  T|array<int, T>|Collection<int, T>|EloquentCollection<int, TModel>  $models
     * @return array<int, T>
     */
    protected function getPrimaryKeys($models): array
    {
        return match (true) {
            $models instanceof EloquentCollection => $models->modelKeys(),
            is_array($models) => $models,
            $models instanceof Collection => $models->toArray(),
            default => Arr::wrap($models),
        };
    }

    /**
     * Get the key for the model.
     *
     * @param \Illuminate\Database\Eloquent\Builder<TModel>|\Illuminate\Database\Eloquent\Relations\Relation<TModel, *, *> $source
     */
    protected function getKey($source): string
    {
        return $source->getModel()->getKeyName();
    }
}
