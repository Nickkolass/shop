<?php

namespace App\Services\Client\API\Payment;

use App\Components\Transport\Consumer\Payment\PaymentTransportInterface;
use App\Dto\PaymentDto;
use App\Models\Order;
use App\Models\OrderPerformer;
use Illuminate\Support\Facades\Gate;

class PaymentService
{

    public function __construct(private readonly PaymentTransportInterface $transport)
    {
    }

    public function pay(PaymentDto $paymentDto): string
    {
        return $this->transport->pay($paymentDto);
    }

    public function payout(PaymentDto $paymentDto, OrderPerformer $order, bool $is_event = false): void
    {
        Gate::forUser($is_event ? $order->saler()->first('id') : auth('api')->user())
            ->authorize('payout', $order);
        $this->transport->refund($paymentDto);
    }

    public function refund(PaymentDto $paymentDto, Order $order, bool $is_event = false): void
    {
        Gate::forUser($is_event ? $order->user()->first() : auth('api')->user())
            ->authorize('refund', $order);
        $this->transport->refund($paymentDto);
    }
}
