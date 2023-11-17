<?php

namespace App\Components\Payment\src\Listeners;

use App\Components\Payment\src\Services\PaymentService;
use App\Events\OrderCanceled;
use App\Events\OrderPerformerCanceled;
use App\Events\OrderPerformerReceived;
use App\Events\OrderReceived;
use Illuminate\Events\Dispatcher;

class PaymentSubscriber
{

    public function __construct(public readonly PaymentService $paymentService)
    {
    }

    public function handleOrderReceived(OrderReceived $event): void
    {
        $this->paymentService->refund($event->order);
    }

    public function handleOrderPerformerReceived(OrderPerformerReceived $event): void
    {
        $this->paymentService->payout($event->order);
    }

    public function handleOrderCanceled(OrderCanceled $event): void
    {
        $this->paymentService->refund($event->order);
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
