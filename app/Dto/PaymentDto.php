<?php

namespace App\Dto;

use App\Http\Requests\Client\Payment\PaymentRequest;
use Illuminate\Support\Facades\Validator;

class PaymentDto
{

    public function __construct(
        public readonly string  $payment_type,
        public readonly int     $order_id,
        public readonly int     $price,
        public readonly ?string $pay_id = null,
        public readonly ?string $payout_token = null,
        public readonly ?string $return_url = null,
    )
    {
        Validator::validate((array)$this, PaymentRequest::getRules());
    }
}
