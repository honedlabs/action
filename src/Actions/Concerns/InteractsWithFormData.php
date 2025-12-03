<?php

declare(strict_types=1);

namespace Honed\Action\Actions\Concerns;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\ValidatedInput;

/**
 * @template TInput
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
}
