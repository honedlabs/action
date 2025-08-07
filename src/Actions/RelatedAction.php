<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithFormData;
use Honed\Action\Contracts\FromRelationship;
use Illuminate\Contracts\Database\Eloquent\SupportsPartialRelations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TRelated of \Illuminate\Database\Eloquent\Model
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\Relation = \Illuminate\Database\Eloquent\Relations\Relation
 * @template TInput of mixed = array<string, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 *
 * @implements \Honed\Action\Contracts\FromRelationship<TModel, TRelation>
 */
abstract class RelatedAction extends DatabaseAction implements FromRelationship
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use InteractsWithFormData;

    /**
     * Act on the related model(s).
     *
     * @param  null|TRelated|TRelation<TRelated, TModel, *>  $related
     * @param  array<string, mixed>  $attributes
     */
    abstract public function act(null|Model|Relation $related, array $attributes): void;

    /**
     * Update the related model.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @return TModel
     */
    public function handle(Model $model, $input = []): Model
    {
        return $this->transaction(
            fn () => $this->execute($model, $input)
        );
    }

    /**
     * Get the relation for the model.
     *
     * @param  TModel  $model
     * @return TRelation
     */
    public function getRelationship(Model $model): Relation
    {
        /** @var TRelation */
        return $model->{$this->relationship()}();
    }

    /**
     * Get the related model(s).
     *
     * @param  TModel  $model
     * @return null|TRelated|TRelation<TRelated, TModel, *>
     */
    public function getRelated(Model $model): Relation|Model|null
    {
        $relationship = $this->getRelationship($model);

        if ($relationship instanceof BelongsTo || $relationship instanceof SupportsPartialRelations) {
            /** @var TRelated|null */
            return $relationship->getResults();
        }

        return $relationship;
    }

    /**
     * Execute the action.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @return TModel
     */
    public function execute(Model $model, $input): Model
    {
        $related = $this->getRelated($model);

        $attributes = $this->attributes($model, $related, $input);

        $this->act($related, $attributes);

        $this->after($model, $related, $input, $attributes);

        return $model;
    }

    /**
     * Prepare the input for the update method.
     *
     * @param  TModel  $model
     * @param  null|TRelated|TRelation<TRelated, TModel, *>  $related
     * @param  TInput  $input
     * @return array<string, mixed>
     */
    public function attributes(Model $model, $related, $input): array
    {
        return $this->normalize($input);
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TModel  $model
     * @param  null|TRelated|TRelation<TRelated, TModel, *>  $related
     * @param  TInput  $input
     * @param  array<string, mixed>  $prepared
     */
    public function after(Model $model, $related, $input, array $prepared): void
    {
        //
    }
}
