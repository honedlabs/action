<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class ForceDeleteAction extends DatabaseAction
{
    /**
     * Force destroy the model.
     *
     * @template T of TModel|\Illuminate\Support\Collection<int, TModel>|\Illuminate\Database\Eloquent\Builder<TModel>|\Illuminate\Database\Eloquent\Relations\Relation<TModel, *, *>
     *
     * @param  T  $model
     * @return T
     */
    public function handle($model)
    {
        $this->transaction(
            fn () => $this->execute($model)
        );

        return $model;
    }

    /**
     * Destroy the model(s).
     *
     * @param  TModel|\Illuminate\Support\Collection<int, TModel>|\Illuminate\Database\Eloquent\Builder<TModel>|\Illuminate\Database\Eloquent\Relations\Relation<TModel, *, *>  $model
     */
    protected function execute($model): void
    {
        match (true) {
            $model instanceof Collection => $model->each->forceDelete(),
            default => $model->forceDelete()
        };

        $this->after($model);
    }

    /**
     * Perform additional logic after the model has been deleted.
     *
     * @param  TModel|\Illuminate\Support\Collection<int, TModel>|\Illuminate\Database\Eloquent\Builder<TModel>|\Illuminate\Database\Eloquent\Relations\Relation<TModel, *, *>  $model
     */
    protected function after($model): void
    {
        //
    }
}
