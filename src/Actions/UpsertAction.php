<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithFormData;
use Honed\Action\Contracts\Upsertable;

use function is_array;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TInput of mixed = array<int, array<string, mixed>>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 */
abstract class UpsertAction extends DatabaseAction implements Upsertable
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use InteractsWithFormData;

    /**
     * Upsert the input data in the database.
     *
     * @param  TInput  $values
     * @return TInput
     */
    public function handle($values)
    {
        $this->transaction(
            fn () => $this->execute($values)
        );

        return $values;
    }

    /**
     * Prepare the input for the update method.
     *
     * @param  TInput  $values
     * @return array<int, array<string, mixed>>
     */
    public function attributes($values): array
    {
        if (! is_array($values)) {
            return [
                $this->only($this->normalize($values)),
            ];
        }

        return $values;
    }

    /**
     * Execute the action.
     *
     * @param  TInput  $values
     */
    public function execute($values): void
    {
        $attributes = $this->attributes($values);

        $source = $this->from();

        if (is_string($source)) {
            $source = $source::query();
        }

        $source->upsert($attributes, $this->uniqueBy(), $this->update());

        $this->after($values, $attributes);
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @param  TInput  $values
     * @param  array<int, array<string, mixed>>  $attributes
     */
    public function after($values, array $attributes): void
    {
        //
    }
}
