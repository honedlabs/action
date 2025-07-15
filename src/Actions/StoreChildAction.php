<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithFormData;
use Honed\Action\Contracts\FromRelationship;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @template TParent of \Illuminate\Database\Eloquent\Model
 * @template TNew of \Illuminate\Database\Eloquent\Model
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\Relation
 * @template TInput of mixed = array<string, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 *
 * @implements \Honed\Action\Contracts\FromRelationship<TParent, TRelation>
 */
abstract class StoreChildAction extends DatabaseAction implements FromRelationship
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use InteractsWithFormData;

    /**
     * Store the input data in the database.
     *
     * @param  TParent  $parent
     * @param  TInput  $input
     * @return TNew $model
     */
    public function handle(Model $parent, $input): Model
    {
        return $this->transaction(
            fn () => $this->execute($parent, $input)
        );
    }

    /**
     * Get the relationship instance.
     *
     * @param  TParent  $model
     * @return TRelation
     */
    public function getRelationship(Model $model): Relation
    {
        /** @var TRelation */
        return $model->{$this->relationship()}();
    }

    /**
     * Prepare the input for the update method.
     *
     * @param  TParent  $parent
     * @param  TInput  $input
     * @return array<string, mixed>
     */
    protected function prepare($parent, $input): array
    {
        return $this->only(
            $this->normalize($input)
        );
    }

    /**
     * Execute the action.
     *
     * @param  TParent  $parent
     * @param  TInput  $input
     * @return TNew
     */
    protected function execute($parent, $input): Model
    {
        $prepared = $this->prepare($parent, $input);

        /** @var TNew */
        $model = $this->getRelationship($parent)
            ->create($prepared);

        $this->after($parent, $model, $input, $prepared);

        return $model;
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TParent  $parent
     * @param  TNew  $model
     * @param  TInput  $input
     * @param  array<string, mixed>  $prepared
     */
    protected function after(Model $parent, Model $model, $input, array $prepared): void
    {
        //
    }
}
