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
    public function execute(Model $model, $parent): Model
    {
        $this->before($model, $parent);

        $model = $this->act($model, $parent);

        $this->after($model, $parent);

        return $model;
    }

    /**
     * Associate the model to the parent.
     *
     * @param  TModel  $model
     * @param  int|string|TParent|null  $parent
     * @return TModel
     */
    public function act(Model $model, $parent): Model
    {
        $model = $this->getRelationship($model)->associate($parent);

        $model->save();

        return $model;
    }

    /**
     * Perform additional logic before the action has been executed.
     *
     * @param  TModel  $model
     * @param  int|string|TParent|null  $parent
     */
    public function before(Model $model, $parent): void {}

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TModel  $model
     * @param  int|string|TParent|null  $parent
     */
    public function after(Model $model, $parent): void {}
}
