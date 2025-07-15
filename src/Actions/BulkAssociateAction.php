<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TParent of \Illuminate\Database\Eloquent\Model
 * @template TAction of \Honed\Action\Actions\AssociateAction<TModel, TParent>
 *
 * @extends \Honed\Action\Actions\BulkAction<TModel, TAction>
 */
abstract class BulkAssociateAction extends BulkAction
{
    /**
     * Associate many models to one parent.
     *
     * @template T of int|string|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     * @param  int|string|TParent|null  $parent
     */
    public function handle($models, $parent): void
    {
        $this->transaction(
            fn () => $this->execute($models, $parent)
        );
    }

    /**
     * Execute the action.
     *
     * @template T of int|string|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     * @param  int|string|TParent|null  $parent
     */
    protected function execute($models, $parent): void
    {
        $action = $this->getAction();

        $this->run(
            $models,
            static fn (Model $model) => $action->handle($model, $parent)
        );

        $this->after($models, $parent);
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @template T of int|string|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     * @param  int|string|TParent|null  $parent
     */
    protected function after($models, $parent): void
    {
        //
    }
}
