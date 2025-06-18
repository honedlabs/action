<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Concerns\CanBeTransaction;
use Honed\Action\Contracts\Action;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class RestoreAction implements Action
{
    use CanBeTransaction;

    /**
     * Restore the model(s).
     *
     * @param  TModel|\Illuminate\Database\Eloquent\Builder<TModel>  $model
     * @return TModel|\Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    public function handle($model)
    {
        return $this->transact(
            fn () => $this->restore($model)
        );
    }

    /**
     * Restore the model(s).
     *
     * @param  TModel|\Illuminate\Database\Eloquent\Builder<TModel>  $model
     * @return TModel|\Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    protected function restore($model)
    {
        $models = $model->restore(); // @phpstan-ignore method.notFound

        $this->after($models);

        return $models;
    }

    /**
     * Perform additional logic after the model(s) have been restored.
     *
     * @param  TModel|\Illuminate\Database\Eloquent\Collection<int, TModel>  $models
     * @return void
     */
    protected function after($models)
    {
        //
    }
}
