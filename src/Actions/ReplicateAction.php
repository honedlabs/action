<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Concerns\CanBeTransaction;
use Honed\Action\Contracts\Action;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\ValidatedInput;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class ReplicateAction implements Action
{
    use CanBeTransaction;

    /**
     * Store the input data in the database.
     *
     * @param  TModel  $model
     * @param  array<string, mixed>|ValidatedInput|FormRequest  $attributes
     * @return TModel $model
     */
    public function handle($model, $attributes = [])
    {
        if ($attributes instanceof FormRequest) {
            /** @var ValidatedInput */
            $attributes = $attributes->safe();
        }

        return $this->transact(
            fn () => $this->replicate($model, $attributes)
        );
    }

    /**
     * Prepare the attributes to override on replication
     *
     * @param  array<string, mixed>|ValidatedInput  $attributes
     * @return array<string, mixed>
     */
    protected function prepare($attributes)
    {
        if ($attributes instanceof ValidatedInput) {
            return $attributes->all();
        }

        return $attributes;
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
     * @param  array<string, mixed>|ValidatedInput  $attributes
     * @return TModel
     */
    protected function replicate($model, $attributes)
    {
        $new = $model->replicate($this->except());

        if (filled($attributes = $this->prepare($attributes))) {
            $new->fill($attributes);
        }

        $new->save();

        $this->after($new, $model);

        return $new;
    }

    /**
     * Perform additional logic after the model has been replicated.
     *
     * @param  TModel  $new
     * @param  TModel  $old
     * @return void
     */
    protected function after($new, $old)
    {
        //
    }
}
