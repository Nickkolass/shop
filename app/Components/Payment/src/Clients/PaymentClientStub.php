<?php

namespace App\Components\Payment\src\Clients;

use App\Components\Payment\src\Dto\CallbackDto;
use App\Components\Payment\src\Services\PaymentCallbackService;
use App\Models\Order;
use App\Models\OrderPerformer;

class PaymentClientStub extends AbstractPaymentClient
{

    const WIDGET_VIEW = 'payment::widget.stub';

    public function __construct(private readonly PaymentCallbackService $callbackService)
    {
    }

    /**
     * @param Order $order
     * @return string $pay_url
     */
    public function pay(Order $order): string
    {
        if (request()->route()->getName() == 'back.api.orders.pay') {
            $requestBody = [
                'event' => self::CALLBACK_EVENT_PAYMENT,
                'order' => $order,
            ];
            $callbackDto = $this->callback($requestBody);
            $this->callbackService->pay($callbackDto);
        }
        return route('client.orders.index', '', false);
    }

    public function payout(OrderPerformer $order): void
    {
        if (request()->route()->getName() == 'admin.orders.payout') {
            $requestBody = [
                'event' => self::CALLBACK_EVENT_PAYOUT,
                'order' => $order,
            ];
            $callbackDto = $this->callback($requestBody);
            $this->callbackService->payout($callbackDto);
        }
    }

    public function refund(Order $order, int $price): void
    {
        if (request()->route()->getName() == 'back.api.orders.refund') {
            $requestBody = [
                'event' => self::CALLBACK_EVENT_REFUND,
                'order' => $order,
            ];
            $callbackDto = $this->callback($requestBody);
            $this->callbackService->refund($callbackDto);
        }
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

    public function getWidget(): string
    {
        return self::WIDGET_VIEW;
    }
}

