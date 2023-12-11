<?php

namespace App\Http\Controllers\Client\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Payment\FrontPayRequest;
use App\Http\Requests\Client\Payment\FrontRefundRequest;
use App\Services\Client\Front\FrontPaymentService;
use Illuminate\Http\RedirectResponse;

class FrontPaymentController extends Controller
{

    public function __construct(private readonly FrontPaymentService $paymentService)
    {
    }

    public function pay(FrontPayRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $pay_url = $this->paymentService->pay($data);
        return redirect()->to($pay_url);
    }

    public function refund(FrontRefundRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->paymentService->refund($data);
        return back();
    }
}
