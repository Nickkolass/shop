<?php

namespace App\Services\Payment;

use App\Components\Payment\PaymentClientInterface;
use App\Models\Order;
use App\Models\OrderPerformer;

class PaymentService
{
    public function __construct(public readonly PaymentClientInterface $paymentClient, public readonly PaymentCallbackService $callbackService)
    {
    }

    public function payment(Order $order): string
    {
        return $this->paymentClient->payment($order);
    }

    public function payout(OrderPerformer $order): void
    {
        $this->paymentClient->payout($order);
    }

    public function refund(Order $order, int $price): void
    {
        $this->paymentClient->refund($order, $price);
    }

    public function callback(): void
    {
        $source = file_get_contents('php://input');
        $requestBody = json_decode((string)$source, true);
        $callbackDto = $this->paymentClient->callback($requestBody);
        $method = $callbackDto->event;
        $this->callbackService->$method($callbackDto);
    }
}
