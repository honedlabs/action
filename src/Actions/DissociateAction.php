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
abstract class DissociateAction extends DatabaseAction implements Relatable
{
    /**
     * Dissociate a model from the parent model.
     *
     * @param  TModel  $model
     * @return TModel
     */
    public function handle(Model $model): Model
    {
        return $this->transact(
            fn () => $this->dissociate($model)
        );
    }

    /**
     * Get the relation for the model.
     *
     * @param  TModel  $model
     * @return BelongsTo<TParent, TModel>
     */
    protected function getRelation(Model $model): BelongsTo
    {
        /** @var BelongsTo<TParent, TModel> */
        return $model->{$this->relationship()}();
    }

    /**
     * Dissociate the parent from the model.
     *
     * @param  TModel  $model
     * @return TModel
     */
    protected function dissociate(Model $model): Model
    {
        $this->getRelation($model)->dissociate();

        $model->save();

        $this->after($model);

        return $model;
    }

    /**
     * Perform additional logic after the model has been detached.
     *
     * @param  TModel  $model
     */
    protected function after(Model $model): void
    {
        //
    }
}
