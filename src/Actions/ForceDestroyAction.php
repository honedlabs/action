<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Concerns\CanBeTransaction;
use Honed\Action\Contracts\Action;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class ForceDestroyAction implements Action
{
    use CanBeTransaction;

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
