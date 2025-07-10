<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;

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

        return $model;
    }

    /**
     * Destroy the model(s).
     *
     * @param  TModel  $model
     */
    protected function forceDestroy($model): void
    {
        $model->forceDelete();

        $this->after($model);
    }

    /**
     * Perform additional logic after the model has been deleted.
     *
     * @param  TModel  $model
     */
    protected function after($model): void
    {
        //
    }
}
