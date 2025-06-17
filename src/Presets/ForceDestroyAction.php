<?php

declare(strict_types=1);

namespace Honed\Action\Presets;

use Honed\Action\Contracts\Actionable;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class ForceDestroyAction implements Actionable
{
    use Concerns\CanBeTransaction;

    /**
     * Force destroy the model.
     *
     * @param  TModel  $model
     * @return void
     */
    public function handle($model)
    {
        $this->transact(
            fn () => $this->forceDestroy($model)
        );

        $this->after($model);
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
