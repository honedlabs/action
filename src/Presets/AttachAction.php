<?php

declare(strict_types=1);

namespace Honed\Action\Presets;

use Honed\Action\Contracts\Actionable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TAttach of \Illuminate\Database\Eloquent\Model
 */
abstract class AttachAction implements Actionable
{
    use Concerns\CanBeTransaction;
    use Concerns\InteractsWithModels;

    /**
     * Get the relation name, must be a belongs to many relationship.
     *
     * @return string
     */
    abstract protected function relationship();

    /**
     * Attach models to the parent model.
     *
     * @param  TModel  $model
     * @param  int|string|TAttach|iterable<int, int|string|TAttach>  $attachments
     * @param  iterable<string, mixed>  $attributes
     * @return void
     */
    public function handle($model, $attachments, $attributes = [])
    {
        $this->transact(
            fn () => $this->attach($model, $attachments, $attributes)
        );
    }

    /**
     * Get the relation for the model.
     *
     * @param  TModel  $model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TModel, TAttach>
     */
    protected function getRelation($model)
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsToMany<TModel, TAttach> */
        return $model->{$this->relationship()}();
    }

    /**
     * Prepare the attachments and attributes for the attach method.
     *
     * @param  int|string|TAttach|array<int, int|string|TAttach>  $attachments
     * @param  array<string, mixed>  $attributes
     * @return array<int|string, array<string, mixed>>
     */
    protected function prepare($attachments, $attributes)
    {
        $attachments = $this->arrayable($attachments);

        return Arr::mapWithKeys(
            $attachments,
            fn ($attachment) => [
                $this->getKey($attachment) => $attributes,
            ]
        );
    }

    /**
     * Store the attachments in the database.
     *
     * @param  TModel  $model
     * @param  int|string|TAttach|iterable<int, int|string|TAttach>  $attachments
     * @param  iterable<string, mixed>  $attributes
     * @return void
     */
    protected function attach($model, $attachments, $attributes)
    {
        $attributes = $this->arrayable($attributes);

        $attaching = $this->prepare($attachments, $attributes);

        $this->getRelation($model)->attach($attaching, touch: $this->shouldTouch());

        $this->after($model, $attachments, $attributes);
    }

    /**
     * Perform additional logic after the model has been attached.
     *
     * @param  TModel  $model
     * @param  int|string|TAttach|array<int, int|string|TAttach>  $attachments
     * @param  array<int|string, mixed>|\Illuminate\Support\ValidatedInput|FormRequest  $attributes
     * @return void
     */
    protected function after($model, $attachments, $attributes)
    {
        //
    }
}
