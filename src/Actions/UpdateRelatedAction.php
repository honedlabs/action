<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TRelated of \Illuminate\Database\Eloquent\Model
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\Relation = \Illuminate\Database\Eloquent\Relations\Relation
 * @template TInput of mixed = array<string, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 *
 * @extends \Honed\Action\Actions\RelatedAction<TModel, TRelated, TRelation, TInput>
 */
abstract class UpdateRelatedAction extends RelatedAction
{
    /**
     * Act on the related model(s).
     *
     * @param  null|TRelated|TRelation<TRelated, TModel, *>  $related
     * @param  array<string, mixed>  $attributes
     */
    public function act(null|Model|Relation $related, array $attributes): void
    {
        $related?->update($attributes);
    }
}
