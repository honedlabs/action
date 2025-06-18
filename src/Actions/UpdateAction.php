<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Concerns\CanBeTransaction;
use Honed\Action\Contracts\Action;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
class UpdateAction implements Action
{
    use CanBeTransaction;

    /**
     * Update the provided model using the input.
     *
     * @param  TModel  $model
     * @param  \Illuminate\Support\ValidatedInput|FormRequest  $input
     * @return TModel $model
     */
    public function handle($model, $input)
    {
        if ($input instanceof FormRequest) {
            /** @var \Illuminate\Support\ValidatedInput */
            $input = $input->safe();
        }

        return $this->transact(
            fn () => $this->update($model, $input)
        );
    }

    /**
     * Prepare the input for the update method.
     *
     * @param  \Illuminate\Support\ValidatedInput  $input
     * @param  TModel  $model
     * @return array<string, mixed>
     */
    protected function prepare($model, $input)
    {
        return $input->all();
    }

    /**
     * Store the record in the database.
     *
     * @param  TModel  $model
     * @param  \Illuminate\Support\ValidatedInput  $input
     * @return TModel
     */
    protected function update($model, $input)
    {
        $prepared = $this->prepare($model, $input);

        $model->update($prepared);

        $this->after($model, $input, $prepared);

        return $model;
    }

    /**
     * Perform additional database transactions after the model has been updated.
     *
     * @param  TModel  $model
     * @param  \Illuminate\Support\ValidatedInput  $input
     * @param  array<string, mixed>  $prepared
     * @return void
     */
    protected function after($model, $input, $prepared)
    {
        //
    }
}
