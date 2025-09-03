<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LinkOrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'links' => ['required', 'array'],
            'links.*' => ['integer', 'exists:links,id'],
        ];
    }
}
