<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class RestoreAction extends DatabaseAction
{
    /**
     * Restore the model(s).
     *
     * @param  TModel  $model
     * @return TModel
     */
    public function handle(Model $model): Model
    {
        return $this->transaction(
            fn () => $this->execute($model)
        );
    }

    /**
     * Execute the action.
     *
     * @param  TModel  $model
     * @return TModel
     */
    public function execute(Model $model): Model
    {
        $this->before($model);

        $model->restore(); // @phpstan-ignore method.notFound

        $this->after($model);

        return $model;
    }

    /**
     * Perform additional logic before the action has been executed.
     *
     * @param  TModel  $model
     */
    public function before(Model $model): void {}

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TModel  $model
     */
    public function after(Model $model): void {}
}
