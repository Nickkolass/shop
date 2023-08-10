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
            'saler_id' => session('user.id'),
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
            'title' => 'required|string|unique:products,title,' . $this->product_id ?? '',
            'description' => 'required|string',
            'category_id' => 'required|integer',
            'saler_id' => 'required|integer',
            'tags' => 'required|array',
        ];
    }
}
