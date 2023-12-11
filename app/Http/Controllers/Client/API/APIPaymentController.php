<?php

namespace App\Http\Controllers\Client\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Payment\APIPayRequest;
use App\Http\Requests\Client\Payment\APIRefundRequest;
use App\Models\Order;
use App\Services\Client\API\Payment\PaymentService;

class APIPaymentController extends Controller
{

    public function __construct(public readonly PaymentService $paymentService)
    {
    }

    public function pay(APIPayRequest $request, Order $order): string
    {
        $data = $request->validated();
        return $this->paymentService->pay($data, $order);
    }

    public function refund(APIRefundRequest $request, Order $order): void
    {
        $data = $request->validated();
        $this->paymentService->refund($data, $order);
    }
}
