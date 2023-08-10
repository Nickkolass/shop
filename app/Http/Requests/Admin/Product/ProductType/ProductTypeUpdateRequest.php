<?php

namespace App\Http\Requests\Admin\Product\ProductType;

use Illuminate\Foundation\Http\FormRequest;

class ProductTypeUpdateRequest extends FormRequest
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
     * Handle a passed validation attempt.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => $this->count > 0 ? $this->is_published ?? 0 : 0,
            'optionValues' => array_filter($this->optionValues),
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
            'price' => 'required|integer',
            'count' => 'required|integer',
            'is_published' => 'bool',
            'preview_image' => 'file',
            'productImages' => 'array',
            'optionValues' => 'required|array',
        ];
    }
}
