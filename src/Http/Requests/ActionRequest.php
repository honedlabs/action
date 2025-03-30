<?php

declare(strict_types=1);

namespace Honed\Action\Http\Requests;

use Honed\Action\ActionFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class ActionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string,array<int,mixed>>
     */
    public function rules()
    {
        $regex = 'regex:/^[\w-]*$/';

        return [
            'name' => ['required', 'string'],
            'type' => ['required', 'in:inline,bulk,page'],

            'only' => ['exclude_unless:type,bulk', 'sometimes', 'array'],
            'except' => ['exclude_unless:type,bulk', 'sometimes', 'array'],
            'all' => ['exclude_unless:type,bulk', 'required', 'boolean'],
            'only.*' => ['sometimes', $regex],
            'except.*' => ['sometimes', $regex],

            'id' => ['exclude_unless:type,inline', 'required', $regex],
        ];
    }

    /**
     * Determine if the action is an inline action.
     *
     * @return bool
     */
    public function isInline()
    {
        return $this->validated('type') === ActionFactory::Inline;
    }

    /**
     * Determine if the action is a bulk action.
     *
     * @return bool
     */
    public function isBulk()
    {
        return $this->validated('type') === ActionFactory::Bulk;
    }

    /**
     * Determine if the action is a page action.
     *
     * @return bool
     */
    public function isPage()
    {
        return $this->validated('type') === ActionFactory::Page;
    }

    /**
     * Get the models to apply the action to.
     *
     * @return array<int,string|int>
     */
    public function ids()
    {
        if ($this->isInline()) {
            /** @var array<int,string|int> */
            return Arr::wrap($this->validated('id'));
        }

        if ($this->isBulk()) {
            /** @var array<int,string|int> */
            return $this->validated('only');
        }

        return [];
    }
}
