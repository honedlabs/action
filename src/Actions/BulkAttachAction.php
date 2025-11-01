<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TAttach of \Illuminate\Database\Eloquent\Model
 * @template TAction of \Honed\Action\Actions\AttachAction<TModel, TAttach, TInput> = \Honed\Action\Actions\AttachAction<TModel, TAttach, TInput>
 * @template TInput of mixed = array<int, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 *
 * @extends \Honed\Action\Actions\BulkAction<TModel, TAction>
 */
abstract class BulkAttachAction extends BulkAction
{
    /**
     * Attach many models to many models.
     *
     * @template T of int|string|null
     * @template U of int|string|TAttach|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     * @param  U|array<int, U>|\Illuminate\Support\Collection<int, U>  $ids
     * @param  TInput  $attributes
     */
    public function handle($models, $ids, $attributes = []): void
    {
        $this->transaction(
            fn () => $this->execute($models, $ids, $attributes)
        );
    }

    /**
     * Execute the action.
     *
     * @template T of int|string|null
     * @template U of int|string|TAttach|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     * @param  U|array<int, U>|\Illuminate\Support\Collection<int, U>  $ids
     * @param  TInput  $attributes
     */
    public function execute($models, $ids, $attributes): void
    {
        $this->before($models, $ids, $attributes);

        $action = $this->getAction();

        $this->run(
            $models,
            static fn (Model $model) => $action->handle($model, $ids, $attributes)
        );

        $this->after($models, $ids, $attributes);
    }

    /**
     * Perform additional logic before the action has been executed.
     *
     * @template T of int|string|null
     * @template U of int|string|TAttach|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     * @param  U|array<int, U>|\Illuminate\Support\Collection<int, U>  $ids
     * @param  TInput  $attributes
     */
    public function before($models, $ids, $attributes): void {}

    /**
     * Perform additional logic after the action has been executed.
     *
     * @template T of int|string|null
     * @template U of int|string|TAttach|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $models
     * @param  U|array<int, U>|\Illuminate\Support\Collection<int, U>  $ids
     * @param  TInput  $attributes
     */
    public function after($models, $ids, $attributes): void {}
}
