<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PropertyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'category_ids' => 'required|array',
            'propertyValues' => 'required|array',
            'propertyValues.*' => 'required|string|filled|distinct',
        ];
    }
}
