<?php

namespace App\Listeners\Payment;

use App\Events\Order\OrderCanceled;
use App\Events\Order\OrderPerformerCanceled;
use App\Events\Order\OrderPerformerReceived;
use App\Events\Order\OrderReceived;
use App\Services\Payment\PaymentService;
use Illuminate\Events\Dispatcher;

class PaymentSubscriber
{

    public function __construct(public readonly PaymentService $paymentService)
    {
    }

    public function handleOrderReceived(OrderReceived $event): void
    {
        $price = $event->order->orderPerformers->whereNotNull('deleted_at')->sum('total_price');
        if (!empty($price)) $this->paymentService->refund($event->order, $price);
    }

    public function handleOrderPerformerReceived(OrderPerformerReceived $event): void
    {
        $this->paymentService->payout($event->order);
    }

    public function handleOrderCanceled(OrderCanceled $event): void
    {
        $price = $event->order->orderPerformers()->onlyTrashed()->sum('total_price');
        if (!empty($price)) $this->paymentService->refund($event->order, $price);
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
