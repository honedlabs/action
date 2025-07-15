<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;

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
    public function handle(Model $model): Model
    {
        return $this->transaction(
            fn () => $this->execute($model)
        );
    }

    /**
     * Touch each given timestamp column.
     *
     * @param  TModel  $model
     * @param  array<int, string>  $touches
     */
    protected function touch(Model $model, array $touches): void
    {
        foreach ($touches as $touch) {
            $model->touch($touch);
        }
    }

    /**
     * Execute the action.
     *
     * @param  TModel  $model
     * @return TModel
     */
    protected function execute(Model $model): Model
    {
        $touches = $this->touches();

        match (true) {
            $touches === true => $model->touchOwners(),
            is_null($touches) => $model->touch(),
            default => $this->touch($model, (array) $touches)
        };

        $this->after($model, $touches);

        return $model;
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TModel  $model
     * @param  true|string|array<int, string>|null  $touches
     */
    protected function after(Model $model, $touches): void
    {
        //
    }
}
