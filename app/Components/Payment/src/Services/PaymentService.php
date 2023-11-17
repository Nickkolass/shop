<?php

namespace App\Components\Payment\src\Services;

use App\Components\Payment\src\Clients\AbstractPaymentClient;
use App\Components\Payment\src\Clients\PaymentClientInterface;
use App\Models\Order;
use App\Models\OrderPerformer;
use Illuminate\Support\Facades\Gate;
use Log;

class PaymentService
{
    public function __construct(public readonly PaymentClientInterface $paymentClient)
    {
    }

    public function pay(Order $order): string
    {
        Gate::authorize('pay', $order);
        return $this->paymentClient->pay($order);
    }

    public function payout(OrderPerformer $order): void
    {
        Gate::forUser(auth()->user() ?? $order->saler()->first('id'))
            ->authorize('payout', $order);
        $order->load('saler:id,card');
        $this->paymentClient->payout($order);
    }

    public function refund(Order $order): void
    {
        $price = $order->orderPerformers()->onlyTrashed()->sum('total_price');
        if (!empty($price)) {
            Gate::forUser(auth()->user() ?? $order->user()->first('id'))
                ->authorize('refund', $order);
            $this->paymentClient->refund($order, $price);
        }
    }

    public function callback(): void
    {
        $source = file_get_contents('php://input');
        $requestBody = json_decode((string)$source, true);
        $callbackDto = $this->paymentClient->callback($requestBody);
        if ($callbackDto->status == AbstractPaymentClient::TRANSACTION_STATUS_SUCCEEDED) {
            $method = $callbackDto->event;
            app(PaymentCallbackService::class)->$method($callbackDto);
        } else Log::info((string)json_encode($callbackDto));
    }
}
