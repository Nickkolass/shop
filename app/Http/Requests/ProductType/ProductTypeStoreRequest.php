<?php

namespace App\Http\Requests\ProductType;

use Illuminate\Foundation\Http\FormRequest;

class ProductTypeStoreRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'price' => 'required|integer',
            'count' => 'required|integer',
            'is_published' => 'nullable|bool',
            'preview_image' => 'required|file',
            'productImages' => 'required|array',
            'optionValues' => 'required|array',
        ];
    }
}
