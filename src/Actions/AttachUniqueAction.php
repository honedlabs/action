<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithFormData;
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
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use InteractsWithFormData;

    /**
     * Sync models to the parent without detaching, using attach.
     *
     * @template T of int|string|TAttach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|Collection<int, T>  $attachments
     * @param  TInput  $input
     * @return TModel
     */
    public function handle(Model $model, $attachments, $input = []): Model
    {
        $this->transaction(
            fn () => $this->execute($model, $attachments, $input)
        );

        return $model;
    }

    /**
     * Get the attributes.
     *
     * @template T of int|string|TAttach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|Collection<int, T>  $attachments
     * @param  TInput  $input
     * @return array<string, mixed>
     */
    public function attributes(Model $model, $attachments, $input): array
    {
        return $this->normalize($input);
    }

    /**
     * Execute the action.
     *
     * @template T of int|string|TAttach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|Collection<int, T>  $attachments
     * @param  TInput  $input
     */
    protected function execute(Model $model, $attachments, $input): void
    {
        $relation = $this->getRelationship($model);

        $existing = $this->getExisting($model, $attachments)->all();

        $attributes = $this->attributes($model, $attachments, $input);

        $relation->attach(
            array_diff($this->parseIds($attachments), $existing),
            $attributes,
        );

        $this->after($model, $attachments, $input, $attributes);
    }

    /**
     * Get the existing attachments for the model.
     *
     * @template T of int|string|TAttach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|Collection<int, T>  $attachments
     * @return Collection<int, int|string>
     */
    protected function getExisting(Model $model, $attachments): Collection
    {
        $query = $this->getRelationship($model)->getQuery();

        $column = $query->qualifyColumn($model->getKeyName());

        /** @var Collection<int, int|string> */
        return $query->pluck($column);
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @template T of int|string|TAttach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|Collection<int, T>  $attachments
     * @param  TInput  $input
     * @param  array<string, mixed>  $attributes
     */
    protected function after(Model $model, $attachments, $input, array $attributes): void
    {
        //
    }
}
