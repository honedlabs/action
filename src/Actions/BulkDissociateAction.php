<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TParent of \Illuminate\Database\Eloquent\Model
 * @template TAction of \Honed\Action\Actions\DissociateAction<TModel, TParent> = \Honed\Action\Actions\DissociateAction<TModel, TParent>
 *
 * @extends \Honed\Action\Actions\BulkAction<TModel, TAction>
 */
abstract class BulkDissociateAction extends BulkAction
{
    /**
     * Associate many models to one parent.
     *
     * @template T of int|string|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     */
    public function handle($models): void
    {
        $this->transaction(
            fn () => $this->execute($models)
        );
    }

    /**
     * Execute the action.
     *
     * @template T of int|string|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     */
    public function execute($models): void
    {
        $this->before($models);

        $action = $this->getAction();

        $this->run(
            $models,
            static fn (Model $model) => $action->handle($model)
        );

        $this->after($models);
    }

    /**
     * Perform additional logic before the action has been executed.
     *
     * @template T of int|string|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     */
    public function before($models): void {}

    /**
     * Perform additional logic after the action has been executed.
     *
     * @template T of int|string|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     */
    public function after($models): void {}
}
