<?php

namespace App\Listeners\Client\API;

use App\Dto\PaymentDto;
use App\Enum\PaymentEventEnum;
use App\Events\OrderCanceled;
use App\Events\OrderPerformerCanceled;
use App\Events\OrderPerformerReceived;
use App\Events\OrderReceived;
use App\Services\Client\API\Payment\PaymentService;
use Illuminate\Events\Dispatcher;

class PaymentSubscriber
{

    public function __construct(public readonly PaymentService $paymentService)
    {
    }

    public function handleOrderReceived(OrderReceived $event): void
    {
        $payment_dto = new PaymentDto(
            payment_type: PaymentEventEnum::PAYMENT_EVENT_REFUND,
            order_id: $event->order->id,
            price: $event->order->orderPerformers->whereNotNull('deleted_at')->sum('total_price'),
            pay_id: $event->order->pay_id,
        );
        if (!empty($payment_dto->price)) $this->paymentService->refund($payment_dto, $event->order, true);
    }

    public function handleOrderPerformerReceived(OrderPerformerReceived $event): void
    {
        $payment_dto = new PaymentDto(
            payment_type: PaymentEventEnum::PAYMENT_EVENT_PAYOUT,
            order_id: $event->order->id,
            price: $event->order->total_price,
            payout_token: $event->order->saler->card['payout_token'],
        );
        $this->paymentService->payout($payment_dto, $event->order, true);
    }

    public function handleOrderCanceled(OrderCanceled $event): void
    {
        $payment_dto = new PaymentDto(
            payment_type: PaymentEventEnum::PAYMENT_EVENT_REFUND,
            order_id: $event->order->id,
            price: $event->order->orderPerformers->whereNotNull('deleted_at')->sum('total_price'),
            pay_id: $event->order->pay_id,
        );
        $this->paymentService->refund($payment_dto, $event->order, true);
    }

    public function handleOrderPerformerCanceled(OrderPerformerCanceled $event): void
    {
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            OrderReceived::class => 'handleOrderReceived',
            OrderPerformerReceived::class => 'handleOrderPerformerReceived',
            OrderCanceled::class => 'handleOrderCanceled',
//            OrderPerformerCanceled::class => 'handleOrderPerformerCanceled',
        ];
    }
}
