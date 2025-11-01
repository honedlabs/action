<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Contracts\Database\Eloquent\Builder;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @internal
 */
abstract class EloquentAction extends DatabaseAction
{
    /**
     * Get the eloquent resource to use as the source.
     *
     * @return class-string<TModel>|\Illuminate\Database\Eloquent\Builder<TModel>|\Illuminate\Database\Eloquent\Relations\Relation<TModel, *, *>
     */
    abstract public function from(): string|Builder;

    /**
     * Get an eloquent builder for the source.
     *
     * @return \Illuminate\Database\Eloquent\Builder<TModel>|\Illuminate\Database\Eloquent\Relations\Relation<TModel, *, *>
     */
    public function query(): Builder
    {
        $source = $this->from();

        if (is_string($source)) {
            /** @var \Illuminate\Database\Eloquent\Builder<TModel> */
            return $source::query();
        }

        return $source;
    }
}
