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
abstract class UpsertAction implements Action
{
    use CanBeTransaction;

    /**
     * Get the model to perform the upsert on.
     *
     * @return class-string<TModel>
     */
    abstract protected function for();

    /**
     * Get the unique by columns for the upsert.
     *
     * @return array<int, string>
     */
    abstract protected function uniqueBy();

    /**
     * Get the columns to update in the upsert.
     *
     * @return array<int, string>
     */
    abstract protected function update();

    /**
     * Upsert the input data in the database.
     *
     * @param  array<int, array<string, mixed>>|ValidatedInput|FormRequest  $values
     * @return array<int, array<string, mixed>>|ValidatedInput
     */
    public function handle($values)
    {
        if ($values instanceof FormRequest) {
            /** @var ValidatedInput */
            $values = $values->safe();
        }

        $this->transact(
            fn () => $this->upsert($values)
        );

        return $values;
    }

    /**
     * Prepare the input for the update method.
     *
     * @param  array<int, array<string, mixed>>|ValidatedInput  $values
     * @return array<int, array<string, mixed>>
     */
    protected function prepare($values)
    {
        if ($values instanceof ValidatedInput) {
            /** @var array<string, mixed> */
            $all = $values->all();

            return [$all];
        }

        return $values;
    }

    /**
     * Upsert the record in the database.
     *
     * @param  array<int, array<string, mixed>>|ValidatedInput  $values
     * @return void
     */
    protected function upsert($values)
    {
        $prepared = $this->prepare($values);

        $class = $this->for();

        (new $class())->query()
            ->upsert($prepared, $this->uniqueBy(), $this->update());

        $this->after($values, $prepared);
    }

    /**
     * Perform additional database transactions after the model has been updated.
     *
     * @param  array<int, array<string, mixed>>|ValidatedInput  $values
     * @param  array<int, array<string, mixed>>  $prepared
     * @return void
     */
    protected function after($values, $prepared)
    {
        //
    }
}
