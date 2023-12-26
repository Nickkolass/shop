<?php

namespace App\Listeners\Client\API;

use App\Events\OrderCanceled;
use App\Events\OrderPerformerCanceled;
use App\Events\OrderPerformerReceived;
use App\Events\OrderReceived;
use App\Services\Client\API\PaymentService;
use Illuminate\Events\Dispatcher;

class PaymentSubscriber
{

    public function __construct(public readonly PaymentService $paymentService)
    {
    }

    public function handleOrderReceived(OrderReceived $event): void
    {
        $data = [
            'order_id' => $event->order->id,
            'pay_id' => $event->order->pay_id,
            'price' => $event->order->orderPerformers->whereNotNull('deleted_at')->sum('total_price'),
        ];
        if(!empty($data['price'])) $this->paymentService->refund($data, $event->order, true);
    }

    public function handleOrderPerformerReceived(OrderPerformerReceived $event): void
    {
        $data = [
            'order_id' => $event->order->id,
            'payout_token' => $event->order->saler->card['payout_token'],
            'price' => $event->order->total_price,
        ];
        $this->paymentService->payout($data, $event->order, true);
    }

    public function handleOrderCanceled(OrderCanceled $event): void
    {
        $data = [
            'order_id' => $event->order->id,
            'pay_id' => $event->order->pay_id,
            'price' => $event->order->total_price,
        ];
        $this->paymentService->refund($data, $event->order, true);
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
