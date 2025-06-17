<?php

declare(strict_types=1);

namespace Honed\Action\Presets;

use Honed\Action\Contracts\Actionable;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TArg of 'model' | 'models' | 'query' | 'relationship' = 'model'
 */
class DestroyAction implements Actionable
{
    use Concerns\CanBeTransaction;

    /**
     * Destroy the model(s).
     *
     * @param TArg is 'model' ? TModel : TArg is 'models' ? iterable<int, TModel> : TArg is 'query' ? \Illuminate\Database\Eloquent\Builder<TModel> : \Illuminate\Database\Eloquent\Relations\Relation<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Model> $model
     * @return void
     */
    public function handle($model)
    {
        $this->transact(
            fn () => $this->destroy($model)
        );

        $this->after($model);
    }

    /**
     * Destroy the model(s).
     *
     * @param TArg is 'model' ? TModel : TArg is 'models' ? iterable<int, TModel> : TArg is 'query' ? \Illuminate\Database\Eloquent\Builder<TModel> : \Illuminate\Database\Eloquent\Relations\Relation<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Model> $model
     * @return void
     */
    protected function destroy($model)
    {
        if (is_iterable($model)) {
            foreach ($model as $item) {
                $item->delete();
            }
        } else {
            $model->delete();
        }
    }

    /**
     * Perform additional logic after the model has been deleted.
     *
     * @param TArg is 'model' ? TModel : TArg is 'models' ? iterable<int, TModel> : TArg is 'query' ? \Illuminate\Database\Eloquent\Builder<TModel> : \Illuminate\Database\Eloquent\Relations\Relation<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Model> $model
     * @return void
     */
    protected function after($model)
    {
        //
    }
}
