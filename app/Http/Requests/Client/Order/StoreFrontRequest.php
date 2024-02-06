<?php

namespace App\Http\Requests\Client\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreFrontRequest extends FormRequest
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
     * Handle a passed validation attempt.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'cart' => session('cart'),
            'return_url' => route('client.orders.index'),
        ]);
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
            'offer' => 'required|string',
            'total_price' => 'required|string',
            'cart' => 'required|array',
            'cart.*' => 'required|int',
        ];
    }
}
