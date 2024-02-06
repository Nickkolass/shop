<?php

namespace App\Components\Transport\Consumer\Payment;

use App\Dto\PaymentDto;

interface PaymentTransportInterface
{
    /**
     * @param array<mixed> $data
     * @return string
     */
    public function getCardWidget(array $data): string;

    /**
     * @param array<mixed> $card
     * @return void
     */
    public function cardValidate(array $card): void;

    public function pay(PaymentDto $paymentDto): string;

    public function payout(PaymentDto $paymentDto): void;

    public function refund(PaymentDto $paymentDto): void;
}
