<?php

namespace App\Services\Client\API\Payment;

use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\OrderPerformer;

class PaymentCallbackService
{

    public function pay(int $order_id, string $payment_id): void
    {
        $order = Order::query()->find($order_id);
        $order->update(['status' => Order::STATUS_PAID, 'pay_id' => $payment_id]);
        $order->orderPerformers()->increment('status');
        event(new OrderPaid($order));
    }

    public function refund(int $order_id, string $payment_id): void
    {
        Order::withTrashed()
            ->where('id', $order_id)
            ->take(1)
            ->update(['refund_id' => $payment_id]);
    }

    public function payout(int $order_id, string $payment_id): void
    {
        OrderPerformer::query()
            ->where('id', $order_id)
            ->take(1)
            ->update(['payout_id' => $payment_id, 'status' => OrderPerformer::STATUS_PAYOUT]);
    }
}
