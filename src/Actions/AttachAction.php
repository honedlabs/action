<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithFormData;
use Honed\Action\Actions\Concerns\InteractsWithModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TAttach of \Illuminate\Database\Eloquent\Model
 * @template TInput of mixed = array<int, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 *
 * @extends \Honed\Action\Actions\BelongsToManyAction<TModel, TAttach>
 */
abstract class AttachAction extends BelongsToManyAction
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use InteractsWithFormData;

    use InteractsWithModels;

    /**
     * Attach models to the model.
     *
     * @template T of int|string|TAttach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $attachments
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
     * Prepare the attachments and attributes for the attach method.
     *
     * @template T of int|string|TAttach|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $attachments
     * @param  TInput  $attributes
     * @return array<int|string, array<string, mixed>>
     */
    protected function prepare($attachments, $attributes): array
    {
        /** @var array<int, int|string|TAttach> */
        $attachments = $this->arrayable($attachments);

        /** @var array<string, mixed> */
        $attributes = $this->only(
            $this->normalize($attributes)
        );

        return Arr::mapWithKeys(
            $attachments,
            fn ($attachment) => [
                $this->getKey($attachment) => $attributes,
            ]
        );
    }

    /**
     * Execute the action.
     *
     * @template T of int|string|TAttach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $attachments
     * @param  TInput  $attributes
     */
    protected function execute(Model $model, $attachments, $attributes): void
    {
        /** @var array<int, int|string|TAttach> */
        $attachments = $this->arrayable($attachments);

        $attaching = $this->prepare($attachments, $attributes);

        $this->getRelationship($model)->attach($attaching, touch: $this->touch());

        $this->after($model, $attachments, $attributes);
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @template T of int|string|TAttach|null
     *
     * @param  TModel  $model
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>  $attachments
     * @param  TInput  $attributes
     */
    protected function after(Model $model, $attachments, $attributes): void
    {
        //
    }
}
