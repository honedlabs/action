<?php

declare(strict_types=1);

namespace Honed\Action\Http\Requests;

use Honed\Action\Support\Constants;
use Honed\Action\Testing\RequestFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class InvokableRequest extends FormRequest
{
    /**
     * The types of actions that can be used in the request.
     *
     * @var list<string>
     */
    protected $types = [
        Constants::INLINE,
        Constants::BULK,
        Constants::PAGE,
    ];

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
            'type' => ['required', Rule::in($this->types)],

            'record' => ['exclude_unless:type,inline', 'required', $regex],

            'only' => ['exclude_unless:type,bulk', 'sometimes', 'array'],
            'except' => ['exclude_unless:type,bulk', 'sometimes', 'array'],
            'all' => ['exclude_unless:type,bulk', 'required', 'boolean'],
            'only.*' => ['sometimes', $regex],
            'except.*' => ['sometimes', $regex],

        ];
    }

    /**
     * Determine if the action is an inline action.
     *
     * @return bool
     */
    public function isInline()
    {
        return $this->validated('type') === Constants::INLINE;
    }

    /**
     * Determine if the action is a bulk action.
     *
     * @return bool
     */
    public function isBulk()
    {
        return $this->validated('type') === Constants::BULK;
    }

    /**
     * Determine if the action is a page action.
     *
     * @return bool
     */
    public function isPage()
    {
        return $this->validated('type') === Constants::PAGE;
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
            return Arr::wrap($this->validated('record'));
        }

        if ($this->isBulk()) {
            /** @var array<int,string|int> */
            return $this->validated('only');
        }

        return [];
    }

    /**
     * Create a new fake action request factory.
     *
     * @return \Honed\Action\Testing\RequestFactory
     */
    public static function fake()
    {
        return RequestFactory::make();
    }
}
