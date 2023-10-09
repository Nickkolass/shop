<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateRelationsRequest extends FormRequest
{
    /**
     * Handle a passed validation attempt.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'propertyValues' => array_filter($this->input('propertyValues', [])),
            'optionValues' => Arr::flatten($this->input('optionValues', [])),
        ]);
    }

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
            'tags' => 'required|array',
            'tags.*' => 'required|int',
            'optionValues' => 'required|array',
            'optionValues.*' => 'required|int',
            'propertyValues' => 'required|array',
            'propertyValues.*' => 'required|string',
        ];
    }
}
