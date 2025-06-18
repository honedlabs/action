<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Concerns\CanBeTransaction;
use Honed\Action\Contracts\Action;
use Honed\Action\Contracts\Relatable;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TParent of \Illuminate\Database\Eloquent\Model
 */
abstract class DissociateAction implements Action, Relatable
{
    use CanBeTransaction;

    /**
     * Dissociate a model from the parent model.
     *
     * @param  TModel  $model
     * @return void
     */
    public function handle($model)
    {
        $this->transact(
            fn () => $this->dissociate($model)
        );

        return $model;
    }

    /**
     * Get the relation for the model.
     *
     * @param  TModel  $model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TParent, TModel>
     */
    protected function getRelation($model)
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo<TParent, TModel> */
        return $model->{$this->relationship()}();
    }

    /**
     * Dissociate the parent from the model.
     *
     * @param  TModel  $model
     * @return void
     */
    protected function dissociate($model)
    {
        $this->getRelation($model)->dissociate();

        $model->save();

        $this->after($model);
    }

    /**
     * Perform additional logic after the model has been detached.
     *
     * @param  TModel  $model
     * @return void
     */
    protected function after($model)
    {
        //
    }
}
