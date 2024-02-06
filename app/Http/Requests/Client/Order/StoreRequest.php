<?php

namespace App\Http\Requests\Client\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'return_url' => 'required|string|url',
            'delivery' => 'required|string',
            'total_price' => 'required|integer',
            'cart' => 'required|array',
            'cart.*' => 'required|int',
        ];
    }
}
