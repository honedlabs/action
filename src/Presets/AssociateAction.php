<?php

declare(strict_types=1);

namespace Honed\Action\Presets;

use Honed\Action\Contracts\Actionable;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TParent of \Illuminate\Database\Eloquent\Model
 */
abstract class AssociateAction implements Actionable
{
    use Concerns\CanBeTransaction;

    /**
     * Get the relation name, must be a belongs to relationship.
     *
     * @return string
     */
    abstract protected function relationship();

    /**
     * Associate a model to the parent model.
     *
     * @param  TModel  $model
     * @param  int|string|TParent  $parent
     * @return void
     */
    public function handle($model, $parent)
    {
        $this->transact(
            fn () => $this->associate($model, $parent)
        );
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
     * Store the parent in the database.
     *
     * @param  TModel  $model
     * @param  int|string|TParent  $parent
     * @return void
     */
    protected function associate($model, $parent)
    {
        $this->getRelation($model)->associate($parent);

        $this->after($model, $parent);
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
