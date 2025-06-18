<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Concerns\CanBeTransaction;
use Honed\Action\Contracts\Action;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 */
abstract class StoreAction implements Action
{
    use CanBeTransaction;

    /**
     * Get the model to store the input data in.
     *
     * @return class-string<TModel>
     */
    abstract protected function for();

    /**
     * Store the input data in the database.
     *
     * @param  \Illuminate\Support\ValidatedInput|FormRequest  $input
     * @return TModel $model
     */
    public function handle($input)
    {
        if ($input instanceof FormRequest) {
            /** @var \Illuminate\Support\ValidatedInput */
            $input = $input->safe();
        }

        return $this->transact(
            fn () => $this->store($input)
        );
    }

    /**
     * Prepare the input for the update method.
     *
     * @param  \Illuminate\Support\ValidatedInput  $input
     * @return array<string, mixed>
     */
    protected function prepare($input)
    {
        return $input->all();
    }

    /**
     * Store the record in the database.
     *
     * @param  \Illuminate\Support\ValidatedInput  $input
     * @return TModel
     */
    protected function store($input)
    {
        $prepared = $this->prepare($input);

        $class = $this->for();

        $model = (new $class())->query()->create($prepared);

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
