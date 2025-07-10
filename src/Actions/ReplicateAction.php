<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Illuminate\Database\Eloquent\Model;

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
    public function handle(Model $model, $attributes = []): Model
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
    protected function prepare($attributes): array
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
    protected function except(): array
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
    protected function replicate(Model $model, $attributes): Model
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
     */
    protected function after(Model $new, Model $old, $attributes): void
    {
        //
    }
}
