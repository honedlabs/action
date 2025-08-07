<?php

declare(strict_types=1);

namespace Honed\Action\Actions;

use Honed\Action\Actions\Concerns\InteractsWithBuilder;
use Honed\Action\Actions\Concerns\InteractsWithFormData;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TInput of mixed = array<string, mixed>|\Illuminate\Support\ValidatedInput|\Illuminate\Foundation\Http\FormRequest
 *
 * @extends \Honed\Action\Actions\EloquentAction<TModel>
 */
abstract class BulkUpdateAction extends EloquentAction
{
    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithBuilder<TModel>
     */
    use InteractsWithBuilder;

    /**
     * @use \Honed\Action\Actions\Concerns\InteractsWithFormData<TInput>
     */
    use InteractsWithFormData;

    /**
     * Update many models.
     *
     * @template T of int|string|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>|\Illuminate\Database\Eloquent\Collection<int, TModel>  $models
     * @param  TInput  $input
     */
    public function handle($models, $input = []): void
    {
        $this->transaction(
            fn () => $this->execute($models, $input)
        );
    }

    /**
     * Retrieve the attributes from the input.
     *
     * @param  TInput  $input
     * @return array<string, mixed>
     */
    public function attributes($input): array
    {
        return $this->normalize($input);
    }

    /**
     * Execute the action.
     *
     * @template T of int|string|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>|\Illuminate\Database\Eloquent\Collection<int, TModel>  $models
     * @param  TInput  $input
     */
    public function execute($models, $input): void
    {
        $attributes = $this->attributes($input);

        $this->getQuery($models)->update($attributes);

        $this->after($models, $input, $attributes);
    }

    /**
     * Perform additional logic after the action has been executed.
     *
     * @template T of int|string|null
     *
     * @param  T|array<int, T>|\Illuminate\Support\Collection<int, T>|\Illuminate\Database\Eloquent\Collection<int, TModel>  $models
     * @param  TInput  $input
     * @param  array<string, mixed>  $attributes
     */
    public function after($models, $input, array $attributes): void
    {
        //
    }
}
