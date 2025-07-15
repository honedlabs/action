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
abstract class AssociateAction extends BelongsToAction
{
    /**
     * Associate a model to the parent model.
     *
     * @param  TModel  $model
     * @param  int|string|TParent|null  $parent
     * @return TModel
     */
    public function handle(Model $model, $parent): Model
    {
        return $this->transaction(
            fn () => $this->execute($model, $parent)
        );
    }

    /**
     * Execute the action.
     *
     * @param  TModel  $model
     * @param  int|string|TParent|null  $parent
     * @return TModel
     */
    protected function execute(Model $model, $parent): Model
    {
        $model = $this->getRelationship($model)->associate($parent);

        $model->save();

        $this->after($model, $parent);

        return $model;
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TModel  $model
     * @param  int|string|TParent|null  $parent
     */
    protected function after(Model $model, $parent): void
    {
        //
    }
}
