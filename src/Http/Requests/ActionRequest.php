<?php

declare(strict_types=1);

namespace Honed\Action\Http\Requests;

use Honed\Action\Creator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string,array<int,mixed>>
     */
    public function rules(): array
    {
        $isInline = Rule::excludeIf(fn ($request) => $request->input('type') === Creator::Inline);
        $isBulk = Rule::excludeIf(fn ($request) => $request->input('type') === Creator::Bulk);

        return [
            'name' => ['required', 'string'],
            'type' => ['required', Rule::in([Creator::Inline, Creator::Bulk, Creator::Page])],

            'only' => [$isInline, 'sometimes', 'array'],
            'except' => [$isInline, 'sometimes', 'array'],
            'all' => [$isInline, 'required', 'boolean'],
            'only.*' => [$isInline, 'sometimes', 'string', 'integer'],
            'except.*' => [$isInline, 'sometimes', 'string', 'integer'],

            'id' => [$isBulk, 'required', 'string', 'integer'],
        ];
    }
}
