<?php

namespace App\Components\Payment;

use App\Components\Payment\Dto\CallbackDto;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Services\Payment\PaymentCallbackService;

class PaymentClientStub extends AbstractPaymentClient
{

    public function __construct(private readonly PaymentCallbackService $callbackService)
    {
    }

    /**
     * @param Order $order
     * @return string $payment_url
     */
    public function payment(Order $order): string
    {
        $requestBody = [
            'event' => self::CALLBACK_EVENT_PAYMENT,
            'order' => $order,
        ];
        $callbackDto = $this->callback($requestBody);
        $this->callbackService->payment($callbackDto);
        return route('client.orders.index', '', false);
    }

    public function payout(OrderPerformer $order): void
    {
        $requestBody = [
            'event' => self::CALLBACK_EVENT_PAYOUT,
            'order' => $order,
        ];
        $callbackDto = $this->callback($requestBody);
        $this->callbackService->payment($callbackDto);
    }

    public function refund(Order $order, int $price): void
    {
        $requestBody = [
            'event' => self::CALLBACK_EVENT_REFUND,
            'order' => $order,
        ];
        $callbackDto = $this->callback($requestBody);
        $this->callbackService->payment($callbackDto);
    }

    public function callback(mixed $requestBody): CallbackDto
    {
        return new CallbackDto(
            id: uniqid('', true),
            event: $requestBody['event'],
            status: self::TRANSACTION_STATUS_SUCCEEDED,
            order: $requestBody['order'],
        );
    }

    public function authorizeCallback(): void
    {
    }
}

