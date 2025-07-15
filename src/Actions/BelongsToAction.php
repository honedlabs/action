<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Contracts\FromRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TParent of \Illuminate\Database\Eloquent\Model
 *
 * @implements \Honed\Action\Contracts\FromRelationship<TModel, \Illuminate\Database\Eloquent\Relations\BelongsTo<TParent, TModel>>
 *
 * @internal
 */
abstract class BelongsToAction extends DatabaseAction implements FromRelationship
{
    /**
     * Get the relation for the model.
     *
     * @param  TModel  $model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TParent, TModel>
     */
    public function getRelationship(Model $model): Relation
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo<TParent, TModel> */
        return $model->{$this->relationship()}();
    }
}
