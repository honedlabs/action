<?php

declare(strict_types=1);

namespace Honed\Action\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\Relation
 */
interface FromRelationship
{
    /**
     * Get the name of the relationship to use.
     */
    public function relationship(): string;

    /**
     * Get the relationship instance.
     *
     * @param  TModel  $model
     * @return TRelation
     */
    public function getRelationship(Model $model): Relation;
}
