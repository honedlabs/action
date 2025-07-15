<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithFormData;
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
    use InteractsWithFormData;

    /**
     * Store the input data in the database.
     *
     * @param  TModel  $model
     * @param  TInput  $attributes
     * @return TModel $model
     */
    public function handle(Model $model, $attributes = []): Model
    {
        return $this->transaction(
            fn () => $this->execute($model, $attributes)
        );
    }

    /**
     * Prepare the attributes to override on replication.
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
     * Execute the action.
     *
     * @param  TModel  $model
     * @param  TInput  $attributes
     * @return TModel
     */
    protected function execute(Model $model, $attributes): Model
    {
        $new = $model->replicate($this->except());

        $prepared = $this->prepare($attributes);

        if (filled($prepared)) {
            $new->fill($prepared);
        }

        $new->save();

        $this->after($new, $model, $attributes, $prepared);

        return $new;
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TModel  $new
     * @param  TModel  $old
     * @param  TInput  $attributes
     * @param  array<string, mixed>  $prepared
     */
    protected function after(Model $new, Model $old, $attributes, array $prepared): void
    {
        //
    }
}
