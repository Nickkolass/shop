<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
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
            'content' => 'required',
            'preview_image' => 'required',
            'price' => 'required|integer',
            'count' => 'required|integer',
            'is_published' => 'required|bool',
            'group_id' => 'nullable|integer',
            'tags' => 'required|array',
            'colors' => 'required|array',
            'product_images' => 'required|array',
            'category_id' => 'required|integer',
        ];
    }
}
