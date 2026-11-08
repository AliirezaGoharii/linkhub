<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LinkOrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'links' => ['required', 'array', 'max:100'],
            'links.*' => [
                'integer',
                // Only ids the authenticated user actually owns are accepted;
                // foreign ids fail validation instead of being silently dropped.
                Rule::exists('links', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }
}
