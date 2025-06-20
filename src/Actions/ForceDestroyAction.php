<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class ForceDestroyAction extends DatabaseAction
{
    /**
     * Force destroy the model.
     *
     * @param  TModel  $model
     * @return TModel
     */
    public function handle($model)
    {
        $this->transact(
            fn () => $this->forceDestroy($model)
        );

        $this->after($model);

        return $model;
    }

    /**
     * Destroy the model(s).
     *
     * @param  TModel  $model
     * @return void
     */
    protected function forceDestroy($model)
    {
        $model->forceDelete();
    }

    /**
     * Perform additional logic after the model has been deleted.
     *
     * @param  TModel  $model
     * @return void
     */
    protected function after($model)
    {
        //
    }
}
