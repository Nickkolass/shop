<?php

namespace App\Enum;

abstract class PaymentEventEnum
{
    const PAYMENT_EVENT_PAY = 'pay';
    const PAYMENT_EVENT_REFUND = 'refund';
    const PAYMENT_EVENT_PAYOUT = 'payout';

    /** @return array<string> */
    public static function getPaymentTypes(): array
    {
        return [
            self::PAYMENT_EVENT_PAY,
            self::PAYMENT_EVENT_REFUND,
            self::PAYMENT_EVENT_PAYOUT,
        ];
    }
}
