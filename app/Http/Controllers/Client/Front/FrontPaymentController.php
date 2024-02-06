<?php

namespace App\Http\Controllers\Client\Front;

use App\Http\Controllers\Controller;
use App\Services\Client\Front\FrontPaymentService;
use Illuminate\Http\RedirectResponse;

class FrontPaymentController extends Controller
{

    public function __construct(private readonly FrontPaymentService $paymentService)
    {
    }

    public function pay(int $order_id): RedirectResponse
    {
        $pay_url = $this->paymentService->pay($order_id);
        return redirect()->to($pay_url);
    }

    public function refund(int $order_id): RedirectResponse
    {
        $this->paymentService->refund($order_id);
        return back();
    }
}
