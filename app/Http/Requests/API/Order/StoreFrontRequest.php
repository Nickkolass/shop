<?php

namespace App\Http\Requests\API\Order;

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
            'payment_status' => true,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'delivery' => 'required|string',
            'payment' => 'required|string',
            'offer' => 'required|string',
            'total_price' => 'required|string',
            'cart' => 'required|array',
            'payment_status' => 'required|bool',
        ];
    }
}
