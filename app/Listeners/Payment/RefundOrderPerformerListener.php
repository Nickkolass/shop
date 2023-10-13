<?php

namespace App\Listeners\Payment;

use App\Components\Yookassa\YooKassaClient;
use App\Events\Order\OrderPerformerCanceled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RefundOrderPerformerListener implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;
    public int $tries = 3;

    public function handle(OrderPerformerCanceled $event): void
    {
        if (!$event->canceler_is_client && !isset($event->order->refund_id)) {
            $refund_id = YooKassaClient::make()->createRefund($event->order->order()->pluck('payment_id')[0], $event->order->total_price);
            $event->order->update(['refund_id' => $refund_id]);
        }
    }
}
