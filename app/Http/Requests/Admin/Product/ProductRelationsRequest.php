<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class ProductRelationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'optionValues' => array_unique(Arr::flatten($this->optionValues ?? [])),
            'propertyValues' => array_filter($this->propertyValues ?? []),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
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
