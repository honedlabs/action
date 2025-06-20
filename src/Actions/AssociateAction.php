<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Contracts\Relatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TParent of \Illuminate\Database\Eloquent\Model
 */
abstract class AssociateAction extends DatabaseAction implements Relatable
{
    /**
     * Associate a model to the parent model.
     *
     * @param  TModel  $model
     * @param  int|string|TParent  $parent
     * @return TModel
     */
    public function handle($model, $parent)
    {
        return $this->transact(
            fn () => $this->associate($model, $parent)
        );
    }

    /**
     * Get the relation for the model.
     *
     * @param  TModel  $model
     * @return BelongsTo<TParent, TModel>
     */
    protected function getRelation($model)
    {
        /** @var BelongsTo<TParent, TModel> */
        return $model->{$this->relationship()}();
    }

    /**
     * Store the parent in the database.
     *
     * @param  TModel  $model
     * @param  int|string|TParent  $parent
     * @return TModel
     */
    protected function associate($model, $parent)
    {
        $model = $this->getRelation($model)->associate($parent);

        $model->save();

        $this->after($model, $parent);

        return $model;
    }

    /**
     * Perform additional logic after the model has been attached.
     *
     * @param  TModel  $model
     * @param  int|string|TParent  $parent
     * @return void
     */
    protected function after($model, $parent)
    {
        //
    }
}
