<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PaymentController extends Controller
{

    public function __construct(public readonly PaymentService $paymentService)
    {
    }

    public function payment(Order $order): string
    {
        $this->authorize('update', $order);
        return $this->paymentService->payment($order);
    }

    /** Заправшивается только покупателями из api */
    public function refund(Order $order): void
    {
        if (empty($order->refund_id)) {
            $this->authorize('update', $order);
            $price = $order->orderPerformers()->onlyTrashed()->sum('total_price');
            if (!empty($price)) $this->paymentService->refund($order, $price);
        }
    }

    /** Запрашивается только продавцами */
    public function payout(OrderPerformer $order): RedirectResponse
    {
        if (empty($order->payout_id)) {
            $this->authorize('update', $order);
            $order->load('saler:id,card');
            $this->paymentService->payout($order);
        }
        return back();
    }

    /** Уведомления от платежной системы */
    public function callback(): Response
    {
        $this->paymentService->paymentClient->authorizeCallback();
        $this->paymentService->callback();
        return response(status: 200)->send();
    }
}
