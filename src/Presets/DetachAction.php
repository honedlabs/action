<?php

declare(strict_types=1);

namespace Honed\Action\Presets;

use Honed\Action\Contracts\Actionable;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TDetach of \Illuminate\Database\Eloquent\Model
 */
abstract class DetachAction implements Actionable
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
     * Detach models from the parent model.
     *
     * @param  TModel  $model
     * @param  int|string|TDetach|array<int, int|string|TDetach>  $detachments
     * @return void
     */
    public function handle($model, $detachments)
    {
        $this->transact(
            fn () => $this->detach($model, $detachments)
        );
    }

    /**
     * Get the relation for the model.
     *
     * @param  TModel  $model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TModel, TDetach>
     */
    protected function getRelation($model)
    {
        /** @var \Illuminate\Database\Eloquent\Relations\BelongsToMany<TModel, TDetach> */
        return $model->{$this->relationship()}();
    }

    /**
     * Convert the detachments to an array of keys.
     *
     * @param  int|string|TDetach|array<int, int|string|TDetach>  $detachments
     * @return array<int, int|string>
     */
    protected function prepare($detachments)
    {
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
     * @return void
     */
    protected function detach($model, $detachments)
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
     * @return void
     */
    protected function after($model, $detachments)
    {
        //
    }
}
