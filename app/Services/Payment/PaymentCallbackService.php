<?php

namespace App\Services\Payment;

use App\Components\Payment\AbstractPaymentClient;
use App\Components\Payment\Dto\CallbackDto;
use App\Events\Order\OrderPaid;
use App\Models\Order;
use Log;

class PaymentCallbackService
{
    public function payment(CallbackDto $callbackDto): void
    {
        if ($callbackDto->status == AbstractPaymentClient::TRANSACTION_STATUS_SUCCEEDED && $callbackDto->order instanceof Order) {
            $callbackDto->order->update(['status' => Order::STATUS_PAID, 'payment_id' => $callbackDto->id]);
            $callbackDto->order->orderPerformers()->increment('status');
            event(new OrderPaid($callbackDto->order, $callbackDto->id));
        } elseif ($callbackDto->status == AbstractPaymentClient::TRANSACTION_STATUS_CANCELED) {
            Log::info((string)json_encode($callbackDto));
        }
    }

    public function refund(CallbackDto $callbackDto): void
    {
        if ($callbackDto->status == AbstractPaymentClient::TRANSACTION_STATUS_SUCCEEDED) {
            $callbackDto->order->update(['refund_id' => $callbackDto->id]);
        } elseif ($callbackDto->status == AbstractPaymentClient::TRANSACTION_STATUS_CANCELED) {
            Log::info((string)json_encode($callbackDto));
        }
    }

    public function payout(CallbackDto $callbackDto): void
    {
        if ($callbackDto->status == AbstractPaymentClient::TRANSACTION_STATUS_SUCCEEDED) {
            $callbackDto->order->update(['payout_id' => $callbackDto->id]);
        } elseif ($callbackDto->status == AbstractPaymentClient::TRANSACTION_STATUS_CANCELED) {
            Log::info((string)json_encode($callbackDto));
        }
    }

    public function deal(CallbackDto $callbackDto): void
    {
    }
}
