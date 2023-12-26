<?php

namespace App\Http\Requests\Client\Payment;

use Illuminate\Foundation\Http\FormRequest;

class APIPayRequest extends FormRequest
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
        $this->merge(['return_url' => route('client.orders.index', '', false)]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required|int',
            'price' => 'required|int',
            'return_url' => 'required|string',
        ];
    }
}
