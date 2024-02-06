<?php

namespace App\Http\Requests\Client\Payment;

use App\Enum\PaymentEventEnum;
use Illuminate\Foundation\Http\FormRequest;

class APICallbackPaymentRequest extends FormRequest
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
        $this->request->remove('status');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return [
            'event' => 'required|string|in:' . implode(',', PaymentEventEnum::getPaymentTypes()),
            'id' => 'required|string',
            'order_id' => 'required|int',
        ];
    }
}
