<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use function is_array;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TInput of mixed = array<int, array<string, mixed>>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 */
abstract class UpsertAction extends DatabaseAction
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use Concerns\InteractsWithFormData;

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
     * @param  TInput  $values
     * @return TInput
     */
    public function handle($values)
    {
        $this->transact(
            fn () => $this->upsert($values)
        );

        return $values;
    }

    /**
     * Prepare the input for the update method.
     *
     * @param  TInput  $values
     * @return array<int, array<string, mixed>>
     */
    protected function prepare($values)
    {
        if (! is_array($values)) {
            return [$this->only(
                $this->normalize($values)
            )];
        }

        return $values;
    }

    /**
     * Upsert the record in the database.
     *
     * @param  TInput  $values
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
     * @param  TInput  $values
     * @param  array<int, array<string, mixed>>  $prepared
     * @return void
     */
    protected function after($values, $prepared)
    {
        //
    }
}
