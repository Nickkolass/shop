<?php

namespace App\Listeners\Payment;

use App\Components\Yookassa\YooKassaClient;
use App\Events\Order\OrderCanceled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RefundOrderListener implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;
    public int $tries = 3;

    public function handle(OrderCanceled $event): void
    {
        $total_price = $event->order->orderPerformers()->whereNotNull('refund_id')->sum('total_price');
        $refund_id = YooKassaClient::make()->createRefund($event->order->payment_id, $total_price);
        $event->order->update(['refund_id' => $refund_id]);
        $event->order->orderPerformers()->update(['refund_id' => $refund_id]);
    }
}
