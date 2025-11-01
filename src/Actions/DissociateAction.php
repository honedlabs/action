<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TParent of \Illuminate\Database\Eloquent\Model
 *
 * @extends \Honed\Action\Actions\BelongsToAction<TModel, TParent>
 */
abstract class DissociateAction extends BelongsToAction
{
    /**
     * Dissociate a model from the parent model.
     *
     * @param  TModel  $model
     * @return TModel
     */
    public function handle(Model $model): Model
    {
        return $this->transaction(
            fn () => $this->execute($model)
        );
    }

    /**
     * Execute the action.
     *
     * @param  TModel  $model
     * @return TModel
     */
    public function execute(Model $model): Model
    {
        $this->before($model);

        $this->getRelationship($model)->dissociate();

        $model->save();

        $this->after($model);

        return $model;
    }

    /**
     * Perform additional logic before the action has been executed.
     *
     * @param  TModel  $model
     */
    public function before(Model $model): void {}

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TModel  $model
     */
    public function after(Model $model): void {}
}
