<?php

declare(strict_types=1);

namespace Workbench\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActiveRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules()
    {
        return [
            'is_active' => 'required|boolean',
        ];
    }
}
