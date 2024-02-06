<?php

namespace App\Http\Controllers\Client\API;

use App\Dto\PaymentDto;
use App\Enum\PaymentEventEnum;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Client\API\Payment\PaymentService;

class APIPaymentController extends Controller
{

    public function __construct(public readonly PaymentService $paymentService)
    {
    }

    public function pay(Order $order): string
    {
        $paymentDto = new PaymentDto(
            payment_type: PaymentEventEnum::PAYMENT_EVENT_PAY,
            order_id: $order->id,
            price: $order->total_price,
            return_url: request()->input('return_url'),
        );
        return $this->paymentService->pay($paymentDto);
    }

    public function refund(Order $order): void
    {
        $paymentDto = new PaymentDto(
            payment_type: PaymentEventEnum::PAYMENT_EVENT_REFUND,
            order_id: $order->id,
            price: $order->orderPerformers()->onlyTrashed()->sum('total_price'),
            pay_id: $order->pay_id,
        );
        $this->paymentService->refund($paymentDto, $order);
    }
}
