<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'title' => 'required|string|unique:products,title,' . $this->input('product_id'),
            'description' => 'required|string',
            'category_id' => 'required|integer',
            'saler_id' => 'required|integer',
        ];
    }
}
