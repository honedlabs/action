<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Support\Collection;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class DeleteAction extends DatabaseAction
{
    /**
     * Delete the model(s).
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
     * Execute the action.
     *
     * @param  TModel|\Illuminate\Support\Collection<int, TModel>|\Illuminate\Database\Eloquent\Builder<TModel>|\Illuminate\Database\Eloquent\Relations\Relation<TModel, *, *>  $model
     */
    protected function execute($model): void
    {
        match (true) {
            $model instanceof Collection => $model->each->delete(),
            default => $model->delete()
        };

        $this->after($model);
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TModel|\Illuminate\Support\Collection<int, TModel>|\Illuminate\Database\Eloquent\Builder<TModel>|\Illuminate\Database\Eloquent\Relations\Relation<TModel, *, *>  $model
     */
    protected function after($model): void
    {
        //
    }
}
