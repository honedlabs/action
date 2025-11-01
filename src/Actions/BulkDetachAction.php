<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TDetach of \Illuminate\Database\Eloquent\Model
 * @template TAction of \Honed\Action\Actions\DetachAction<TModel, TDetach> = \Honed\Action\Actions\DetachAction<TModel, TDetach>
 *
 * @extends \Honed\Action\Actions\BulkAction<TModel, TAction>
 */
abstract class BulkDetachAction extends BulkAction
{
    /**
     * Detach many models to many models.
     *
     * @template T of int|string|null
     * @template U of int|string|TDetach|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     * @param  U|array<int, U>|\Illuminate\Support\Collection<int, U>  $ids
     */
    public function handle($models, $ids): void
    {
        $this->transaction(
            fn () => $this->execute($models, $ids)
        );
    }

    /**
     * Execute the action.
     *
     * @template T of int|string|null
     * @template U of int|string|TDetach|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     * @param  U|array<int, U>|\Illuminate\Support\Collection<int, U>  $ids
     */
    public function execute($models, $ids): void
    {
        $this->before($models, $ids);

        $action = $this->getAction();

        $this->run(
            $models,
            static fn (Model $model) => $action->handle($model, $ids)
        );

        $this->after($models, $ids);
    }

    /**
     * Perform additional logic before the action has been executed.
     *
     * @template T of int|string|null
     * @template U of int|string|TDetach|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     * @param  U|array<int, U>|\Illuminate\Support\Collection<int, U>  $ids
     */
    public function before($models, $ids): void {}

    /**
     * Perform additional logic after the action has been executed.
     *
     * @template T of int|string|null
     * @template U of int|string|TDetach|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     * @param  U|array<int, U>|\Illuminate\Support\Collection<int, U>  $ids
     */
    public function after($models, $ids): void {}
}
