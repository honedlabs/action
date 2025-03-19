<?php

declare(strict_types=1);

namespace Honed\Action\Http\Requests;

use Honed\Action\ActionFactory;
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
        $bulk = Rule::excludeIf(\in_array($this->input('type'), [ActionFactory::Inline, ActionFactory::Page]));
        $inline = Rule::excludeIf(\in_array($this->input('type'), [ActionFactory::Bulk, ActionFactory::Page]));
        $regex = 'regex:/^[\w-]*$/';

        return [
            'name' => ['required', 'string'],
            'type' => ['required', Rule::in([ActionFactory::Inline, ActionFactory::Bulk, ActionFactory::Page])],

            'only' => [$bulk, 'sometimes', 'array'],
            'except' => [$bulk, 'sometimes', 'array'],
            'all' => [$bulk, 'required', 'boolean'],
            'only.*' => [$bulk, 'sometimes', $regex],
            'except.*' => [$bulk, 'sometimes', $regex],

            'id' => [$inline, 'required', $regex],
        ];
    }

    /**
     * Determine if the action is an inline action.
     */
    public function isInline(): bool
    {
        return $this->validated('type') === ActionFactory::Inline;
    }

    /**
     * Determine if the action is a bulk action.
     */
    public function isBulk(): bool
    {
        return $this->validated('type') === ActionFactory::Bulk;
    }

    /**
     * Determine if the action is a page action.
     */
    public function isPage(): bool
    {
        return $this->validated('type') === ActionFactory::Page;
    }
}
