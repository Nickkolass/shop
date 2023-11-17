<?php

namespace App\Components\Payment\src\Services;

use App\Components\Payment\src\Dto\CallbackDto;
use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\OrderPerformer;

class PaymentCallbackService
{
    public function pay(CallbackDto $callbackDto): void
    {
        /** @var Order $order */
        $order = $callbackDto->order;
        $order->update(['status' => Order::STATUS_PAID, 'pay_id' => $callbackDto->id]);
        $order->orderPerformers()->increment('status');
        event(new OrderPaid($order, $callbackDto->id));
    }

    public function refund(CallbackDto $callbackDto): void
    {
        $callbackDto->order->update(['refund_id' => $callbackDto->id]);
    }

    public function payout(CallbackDto $callbackDto): void
    {
        $callbackDto->order->update(['payout_id' => $callbackDto->id, 'status' => OrderPerformer::STATUS_PAYOUT]);
    }

    public function deal(CallbackDto $callbackDto): void
    {
    }
}
