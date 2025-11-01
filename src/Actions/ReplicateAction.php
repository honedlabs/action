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
     * @param  TInput  $input
     * @return TModel $model
     */
    public function handle(Model $model, $input = []): Model
    {
        return $this->transaction(
            fn () => $this->execute($model, $input)
        );
    }

    /**
     * Prepare the attributes to override on replication.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @return array<string, mixed>
     */
    public function attributes(Model $model, $input): array
    {
        return $this->normalize($input);
    }

    /**
     * Get the attributes to exclude from the replication.
     *
     * @return list<string>
     */
    public function except(): array
    {
        return [];
    }

    /**
     * Execute the action.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     * @return TModel
     */
    public function execute(Model $model, $input): Model
    {
        $this->before($model, $input);

        $new = $model->replicate($this->except());

        $attributes = $this->attributes($model, $input);

        if (filled($attributes)) {
            $new->fill($attributes);
        }

        $new->save();

        $this->after($new, $model, $input, $attributes);

        return $new;
    }

    /**
     * Perform additional logic before the action has been executed.
     *
     * @param  TModel  $model
     * @param  TInput  $input
     */
    public function before(Model $model, $input): void {}

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TModel  $new
     * @param  TModel  $old
     * @param  TInput  $input
     * @param  array<string, mixed>  $attributes
     */
    public function after(Model $new, Model $old, $input, array $attributes): void {}
}
