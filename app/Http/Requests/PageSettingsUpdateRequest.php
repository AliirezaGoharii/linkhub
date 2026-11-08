<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageSettingsUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'theme_color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
            'bio' => ['nullable', 'string', 'max:280'],
        ];
    }
}
