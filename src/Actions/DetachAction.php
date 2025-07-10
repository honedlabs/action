<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Contracts\Relatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TDetach of \Illuminate\Database\Eloquent\Model
 */
abstract class DetachAction extends DatabaseAction implements Relatable
{
    use Concerns\InteractsWithModels;

    /**
     * Detach models from the parent model.
     *
     * @param  TModel  $model
     * @param  int|string|TDetach|array<int, int|string|TDetach>  $detachments
     * @return TModel
     */
    public function handle(Model $model, $detachments): Model
    {
        $this->transact(
            fn () => $this->detach($model, $detachments)
        );

        return $model;
    }

    /**
     * Get the relation for the model.
     *
     * @param  TModel  $model
     * @return BelongsToMany<TModel, TDetach>
     */
    protected function getRelation(Model $model): BelongsToMany
    {
        /** @var BelongsToMany<TModel, TDetach> */
        return $model->{$this->relationship()}();
    }

    /**
     * Convert the detachments to an array of keys.
     *
     * @param  int|string|TDetach|array<int, int|string|TDetach>  $detachments
     * @return array<int, int|string>
     */
    protected function prepare($detachments): array
    {
        /** @var array<int, int|string|TDetach> */
        $detachments = $this->arrayable($detachments);

        return array_map(
            fn ($attachment) => $this->getKey($attachment),
            $detachments
        );
    }

    /**
     * Store the detachments in the database.
     *
     * @param  TModel  $model
     * @param  int|string|TDetach|array<int, int|string|TDetach>  $detachments
     */
    protected function detach(Model $model, $detachments): void
    {
        $detaching = $this->prepare($detachments);

        $this->getRelation($model)->detach($detaching, $this->shouldTouch());

        $this->after($model, $detachments);
    }

    /**
     * Perform additional logic after the model has been detached.
     *
     * @param  TModel  $model
     * @param  int|string|TDetach|array<int, int|string|TDetach>  $detachments
     */
    protected function after(Model $model, $detachments): void
    {
        //
    }
}
