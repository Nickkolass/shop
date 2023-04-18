<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'title' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|string',
            'count' => 'required|string',
            'group_id' => 'nullable|integer',
            'preview_image' => 'nullable',
            
            'productImages' => 'nullable|array',
            
            'optionValues' => 'required|array',
            'propertyValues' => 'required|array',
            'tags' => 'required|array',
        ];
    }
}
