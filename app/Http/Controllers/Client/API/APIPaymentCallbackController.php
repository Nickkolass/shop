<?php

namespace App\Http\Controllers\Client\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Payment\APICallbackPaymentRequest;
use App\Services\Client\API\Payment\PaymentCallbackService;

class APIPaymentCallbackController extends Controller
{

    public function __construct(public readonly PaymentCallbackService $callbackService)
    {
    }

    public function callback(APICallbackPaymentRequest $request): void
    {
        $data = $request->validated();
        $method = $data['event'];
        $this->callbackService->$method($data['order_id'], $data['id']);
    }
}
