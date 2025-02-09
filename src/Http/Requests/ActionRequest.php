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
        $bulk = Rule::excludeIf(\in_array($this->input('type'), [Creator::Inline, Creator::Page]));
        $inline = Rule::excludeIf(\in_array($this->input('type'), [Creator::Bulk, Creator::Page]));
        $regex = 'regex:/^[\w-]*$/';

        return [
            'name' => ['required', 'string'],
            'type' => ['required', Rule::in([Creator::Inline, Creator::Bulk, Creator::Page])],

            'only' => [$bulk, 'sometimes', 'array'],
            'except' => [$bulk, 'sometimes', 'array'],
            'all' => [$bulk, 'required', 'boolean'],
            'only.*' => [$bulk, 'sometimes', $regex],
            'except.*' => [$bulk, 'sometimes', $regex],

            'id' => [$inline, 'required', $regex],
        ];
    }
}
