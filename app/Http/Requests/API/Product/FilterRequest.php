<?php

namespace App\Http\Requests\API\Product;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
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
            'category' => 'string',
            'tags' => 'array',
            'colors' => 'array',
            'salers' => 'array',
            'prices' => 'array',

            // 'descriprion' => 'nullable|array',
            // 'content' => 'nullable|array',
            // 'price' => 'nullable|array',
            // 'count' => 'nullable|array',
            // 'is_published' => 'nullable|array',
            // 'saler' => 'nullable|array',
            // 'group' => 'nullable|array',
            // 'page' => 'required|integer',
        ];
    }
}
