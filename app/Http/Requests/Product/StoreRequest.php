<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
                'types' => 'array',
                'types.*.optionValues' => 'required|array',
                'types.*.price' => 'required|int',
                'types.*.count' => 'required|int',
                'types.*.is_published' => 'nullable|bool',
                'types.*.preview_image' => 'required|file',
                'types.*.productImages' => 'required|array',
        ];
    }
}
