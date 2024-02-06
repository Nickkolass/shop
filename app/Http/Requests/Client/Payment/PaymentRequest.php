<?php

namespace App\Http\Requests\Client\Payment;

use App\Enum\PaymentEventEnum;
use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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

    /** @return array<mixed> */
    public static function getRules(): array
    {
        return [
            'payment_type' => 'required|string|in:' . implode(',', PaymentEventEnum::getPaymentTypes()),
            'order_id' => 'required|int',
            'price' => 'required|int',
            'return_url' => 'nullable|url|prohibits:payout_token,pay_id|required_if:payment_type,' . PaymentEventEnum::PAYMENT_EVENT_PAY,
            'payout_token' => 'nullable|string|prohibits:return_url,pay_id|required_if:payment_type,' . PaymentEventEnum::PAYMENT_EVENT_PAYOUT,
            'pay_id' => 'nullable|string|prohibits:payout_token,return_url|required_if:payment_type,' . PaymentEventEnum::PAYMENT_EVENT_REFUND,
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<mixed>
     */
    public function rules(): array
    {
        return self::getRules();
    }
}
