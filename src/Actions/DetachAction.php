<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithModels;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TDetach of \Illuminate\Database\Eloquent\Model
 *
 * @extends \Honed\Action\Actions\BelongsToManyAction<TModel, TDetach>
 */
abstract class DetachAction extends BelongsToManyAction
{
    use InteractsWithModels;

    /**
     * Detach models from the parent model.
     *
     * @template T of int|string|TDetach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $detachments
     * @return TModel
     */
    public function handle(Model $model, $detachments): Model
    {
        $this->transaction(
            fn () => $this->execute($model, $detachments)
        );

        return $model;
    }

    /**
     * Convert the detachments to an array of keys.
     *
     * @template T of int|string|TDetach|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $detachments
     * @return array<int, int|string>
     */
    public function attributes($detachments): array
    {
        /** @var array<int, int|string|TDetach> */
        $detachments = $this->arrayable($detachments);

        return array_map(
            fn ($attachment) => $this->getKey($attachment),
            $detachments
        );
    }

    /**
     * Execute the action.
     *
     * @template T of int|string|TDetach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $detachments
     */
    public function execute(Model $model, $detachments): void
    {
        $this->before($model, $detachments);

        $detaching = $this->attributes($detachments);

        $this->getRelationship($model)->detach($detaching, $this->touch());

        $this->after($model, $detachments);
    }

    /**
     * Perform additional logic before the action has been executed.
     *
     * @template T of int|string|TDetach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $detachments
     */
    public function before(Model $model, $detachments): void {}

    /**
     * Perform additional logic after the action has been executed.
     *
     * @template T of int|string|TDetach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $detachments
     */
    public function after(Model $model, $detachments): void {}
}
