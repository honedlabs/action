<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TInput of mixed = array<string, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 */
class ReplicateAction extends DatabaseAction
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use Concerns\InteractsWithFormData;

    /**
     * Store the input data in the database.
     *
     * @param  TModel  $model
     * @param  TInput  $attributes
     * @return TModel $model
     */
    public function handle($model, $attributes = [])
    {
        return $this->transact(
            fn () => $this->replicate($model, $attributes)
        );
    }

    /**
     * Prepare the attributes to override on replication
     *
     * @param  TInput  $attributes
     * @return array<string, mixed>
     */
    protected function prepare($attributes)
    {
        return $this->only(
            $this->normalize($attributes)
        );
    }

    /**
     * Get the attributes to exclude from the replication.
     *
     * @return array<int, string>
     */
    protected function except()
    {
        return [];
    }

    /**
     * Store the record in the database.
     *
     * @param  TModel  $model
     * @param  TInput  $attributes
     * @return TModel
     */
    protected function replicate($model, $attributes)
    {
        $new = $model->replicate($this->except());

        $prepared = $this->prepare($attributes);

        if (filled($prepared)) {
            $new->fill($prepared);
        }

        $new->save();

        $this->after($new, $model, $attributes);

        return $new;
    }

    /**
     * Perform additional logic after the model has been replicated.
     *
     * @param  TModel  $new
     * @param  TModel  $old
     * @param  TInput  $attributes
     * @return void
     */
    protected function after($new, $old, $attributes)
    {
        //
    }
}
