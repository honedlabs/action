<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Contracts\FromRelationship;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TMany of \Illuminate\Database\Eloquent\Model
 *
 * @implements \Honed\Action\Contracts\FromRelationship<TModel, \Illuminate\Database\Eloquent\Relations\BelongsToMany<TModel, TMany>>
 *
 * @internal
 */
abstract class BelongsToManyAction extends DatabaseAction implements FromRelationship
{
    /**
     * Get the relation for the model.
     *
     * @param  TModel  $model
     * @return BelongsToMany<TModel, TMany>
     */
    public function getRelationship(Model $model): Relation
    {
        /** @var BelongsToMany<TModel, TMany> */
        return $model->{$this->relationship()}();
    }

    /**
     * @template T of int|string|TMany|null
     *
     * @param  T|array<int, T>|Collection<int, T>  $ids
     * @return array<int, int|string>
     */
    public function parseIds($ids)
    {
        if ($ids instanceof EloquentCollection) {
            return $ids->modelKeys();
        }

        $ids = match (true) {
            $ids instanceof Collection => $ids->all(),
            is_array($ids) => $ids,
            default => Arr::wrap($ids),
        };

        return array_map(
            static fn ($id) => $id instanceof Model ? $id->getKey() : $id,
            $ids,
        );
    }
}
