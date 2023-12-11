<?php

namespace App\Http\Controllers\Client\API;

use App\Events\OrderPaid;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Payment\CallbackPaymentRequest;
use App\Models\Order;
use App\Models\OrderPerformer;

class APIPaymentCallbackController extends Controller
{

    public function callback(CallbackPaymentRequest $request): void
    {
        $this->authorizeCallback();
        $data = $request->validated();
        switch ($data) {
            case('pay'):
                $this->pay($data);
                break;
            case('refund'):
                $this->refund($data);
                break;
            case('payout'):
                $this->payout($data);
                break;
        }
    }

    private function authorizeCallback(): void
    {
    }

    /**
     * @param array{order_id: int, payment_id: string, event: string} $data
     * @return void
     */
    private function pay(array $data): void
    {
        $order = Order::query()->firstWhere('id', $data['order_id']);
        $order->update(['status' => Order::STATUS_PAID, 'pay_id' => $data['payment_id']]);
        $order->orderPerformers()->increment('status');
        event(new OrderPaid($order));
    }

    /**
     * @param array{order_id: int, payment_id: string, event: string} $data
     * @return void
     */
    private function refund(array $data): void
    {
        Order::query()
            ->take(1)
            ->where('id', $data['order_id'])
            ->update(['refund_id' => $data['payment_id']]);
    }

    /**
     * @param array{order_id: int, payment_id: string, event: string} $data
     * @return void
     */
    private function payout(array $data): void
    {
        OrderPerformer::query()
            ->take(1)
            ->where('id', $data['order_id'])
            ->update(['payout_id' => $data['payment_id'], 'status' => OrderPerformer::STATUS_PAYOUT]);
    }
}
