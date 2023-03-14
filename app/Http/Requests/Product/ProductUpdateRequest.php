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
            'content' => 'required',
            'preview_image' => 'nullable',
            'price' => 'required|integer',
            'count' => 'required|integer',
            'is_published' => 'required|bool',
            'group_id' => 'nullable|integer',
            'tags' => 'required|array',
            'color_id' => 'required|integer',
            'productImages' => 'nullable|array',
            'saler_id' => 'nullable',
            'category_id' => 'nullable|integer',
        ];
    }
}
