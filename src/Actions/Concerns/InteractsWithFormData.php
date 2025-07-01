<?php

declare(strict_types=1);

namespace Honed\Action\Actions\Concerns;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\ValidatedInput;

/**
 * @template TInput of mixed
 */
trait InteractsWithFormData
{
    /**
     * Normalize the input data.
     *
     * @param  TInput  $input
     * @return array<string, mixed>
     */
    protected function normalize($input): array
    {
        return match (true) {
            $input instanceof Arrayable => $input->toArray(),
            $input instanceof ValidatedInput => $input->all(), // @phpstan-ignore instanceof.alwaysFalse
            $input instanceof FormRequest => $input->safe()->all(), // @phpstan-ignore instanceof.alwaysFalse
            default => $input,
        };
    }

    /**
     * Get the validated input data.
     *
     * @param  TInput  $input
     */
    protected function input($input): ValidatedInput
    {
        return match (true) {
            $input instanceof FormRequest => $input->safe(),
            $input instanceof ValidatedInput => $input,
            default => new ValidatedInput($input),
        };
    }

    /**
     * Define the fields to be retrieved from the input.
     *
     * @return array<string>
     */
    protected function fields(): array
    {
        return [];
    }

    /**
     * Retrieve only the fields defined in the `only` method.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    protected function only(array $input): array
    {
        $fields = $this->fields();

        return filled($fields)
            ? Arr::only($input, $fields)
            : $input;
    }
}
