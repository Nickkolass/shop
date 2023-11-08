<?php

namespace App\Components\Payment;

use App\Components\Payment\Dto\CallbackDto;
use App\Models\Order;
use App\Models\OrderPerformer;

interface PaymentClientInterface
{

    public static function getConnection(): ?string;

    /**
     * @param Order $order
     * @return string $payment_url
     */
    public function payment(Order $order): string;

    public function payout(OrderPerformer $order): void;

    public function refund(Order $order, int $price): void;

    public function authorizeCallback(): void;

    public function callback(mixed $requestBody): CallbackDto;
}
