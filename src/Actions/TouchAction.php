<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class TouchAction extends DatabaseAction
{
    /**
     * Get the column(s) to touch. By default, it will update the updated at
     * at columns. You can select a single column, multiple columns by passing
     * the names. Or, you can pass `true` to touch the owner relationships.
     *
     * @return true|string|array<int, string>|null
     */
    public function touches()
    {
        return null;
    }

    /**
     * Update the provided model using the input.
     *
     * @param  TModel  $model
     * @return TModel $model
     */
    public function handle($model)
    {
        return $this->transact(
            fn () => $this->touch($model)
        );
    }

    /**
     * Touch each given timestamp column.
     *
     * @param  TModel  $model
     * @param  array<int, string>  $touches
     * @return void
     */
    protected function touchEach($model, $touches)
    {
        foreach ($touches as $touch) {
            $model->touch($touch);
        }
    }

    /**
     * Update the record in the database.
     *
     * @param  TModel  $model
     * @return TModel
     */
    protected function touch($model)
    {
        $touches = $this->touches();

        match (true) {
            $touches === true => $model->touchOwners(),
            is_null($touches) => $model->touch(),
            default => $this->touchEach($model, (array) $touches)
        };

        $this->after($model, $touches);

        return $model;
    }

    /**
     * Perform additional database transactions after the model has been updated.
     *
     * @param  TModel  $model
     * @param  true|string|array<int, string>|null  $touches
     * @return void
     */
    protected function after($model, $touches)
    {
        //
    }
}
