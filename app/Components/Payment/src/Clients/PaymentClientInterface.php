<?php

namespace App\Components\Payment\src\Clients;

use App\Components\Payment\src\Dto\CallbackDto;
use App\Models\Order;
use App\Models\OrderPerformer;

interface PaymentClientInterface
{

    public static function getConnection(): ?string;

    /**
     * @param Order $order
     * @return string $pay_url
     */
    public function pay(Order $order): string;

    public function payout(OrderPerformer $order): void;

    public function refund(Order $order, int $price): void;

    public function authorizeCallback(): void;

    public function callback(mixed $requestBody): CallbackDto;

    public function getWidget(): string;
}
