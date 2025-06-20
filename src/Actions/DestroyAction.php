<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Support\Collection;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TType of TModel|\Illuminate\Database\Eloquent\Collection<int, TModel>|\Illuminate\Database\Eloquent\Builder<TModel>|\Illuminate\Database\Eloquent\Relations\Relation<TModel, \Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Model> = TModel
 */
class DestroyAction extends DatabaseAction
{
    /**
     * Destroy the model(s).
     *
     * @param  TType  $model
     * @return TType
     */
    public function handle($model)
    {
        $this->transact(
            fn () => $this->destroy($model)
        );

        return $model;
    }

    /**
     * Destroy the model(s).
     *
     * @param  TType  $model
     * @return void
     */
    protected function destroy($model)
    {
        if ($model instanceof Collection) {
            foreach ($model as $item) {
                $item->delete();
            }
        } else {
            $model->delete();
        }

        $this->after($model);
    }

    /**
     * Perform additional logic after the model has been deleted.
     *
     * @param  TType  $model
     * @return void
     */
    protected function after($model)
    {
        //
    }
}
