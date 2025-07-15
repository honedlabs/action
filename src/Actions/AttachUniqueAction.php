<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TAttach of \Illuminate\Database\Eloquent\Model
 * @template TInput of mixed = array<int, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 *
 * @extends \Honed\Action\Actions\BelongsToManyAction<TModel, TAttach>
 */
abstract class AttachUniqueAction extends BelongsToManyAction
{
    /**
     * Sync models to the parent without detaching, using attach.
     *
     * @template T of int|string|TAttach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|Collection<int, T>  $attachments
     * @param  TInput  $attributes
     * @return TModel
     */
    public function handle(Model $model, $attachments, $attributes = []): Model
    {
        $this->transaction(
            fn () => $this->execute($model, $attachments, $attributes)
        );

        return $model;
    }

    /**
     * Execute the action.
     *
     * @template T of int|string|TAttach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|Collection<int, T>  $attachments
     * @param  TInput  $attributes
     */
    protected function execute(Model $model, $attachments, $attributes): void
    {
        $relation = $this->getRelationship($model);

        $existing = $this->getExisting($model)->all();

        $relation->attach(
            array_diff($this->parseIds($attachments), $existing),
            $attributes,
        );

        $this->after($model, $attachments, $attributes);
    }

    /**
     * Get the existing attachments for the model.
     *
     * @param  TModel  $model
     * @return Collection<int, int|string>
     */
    protected function getExisting(Model $model): Collection
    {
        /** @var Collection<int, int|string> */
        return $this->getRelationship($model)
            ->getQuery()
            ->getQuery()
            ->pluck('id');
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @template T of int|string|TAttach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|Collection<int, T>  $attachments
     * @param  TInput  $attributes
     */
    protected function after(Model $model, $attachments, $attributes): void
    {
        //
    }
}
