<?php

namespace App\Http\Controllers\Client\API;

use App\Events\OrderPaid;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Payment\CallbackPaymentRequest;
use App\Models\Order;
use App\Models\OrderPerformer;
use Illuminate\Http\Response;

class APIPaymentCallbackController extends Controller
{

    public function callback(CallbackPaymentRequest $request): Response
    {
        $data = $request->validated();
        switch ($data['event']) {
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
        return response(status: 200)->send();
    }

    /**
     * @param array{order_id: int, id: string, event: string} $data
     * @return void
     */
    private function pay(array $data): void
    {
        $order = Order::query()->firstWhere('id', $data['order_id']);
        $order->update(['status' => Order::STATUS_PAID, 'pay_id' => $data['id']]);
        $order->orderPerformers()->increment('status');
        event(new OrderPaid($order));
    }

    /**
     * @param array{order_id: int, id: string, event: string} $data
     * @return void
     */
    private function refund(array $data): void
    {
        Order::query()
            ->find($data['order_id'], 'id')
            ->update(['refund_id' => $data['id']]);
    }

    /**
     * @param array{order_id: int, id: string, event: string} $data
     * @return void
     */
    private function payout(array $data): void
    {
        OrderPerformer::query()
            ->find($data['order_id'], 'id')
            ->update(['payout_id' => $data['id'], 'status' => OrderPerformer::STATUS_PAYOUT]);
    }
}
