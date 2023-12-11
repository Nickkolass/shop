<?php

namespace App\Services\Client\API\Payment;

use App\Components\HttpClient\HttpClientInterface;
use App\Models\Order;
use App\Models\OrderPerformer;
use Illuminate\Support\Facades\Gate;

class PaymentService
{

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    /**
     * @param array{order_id: int, price: int, return_url: string} $data
     * @param Order $order
     * @return string
     */
    public function pay(array $data, Order $order): string
    {
        Gate::authorize('pay', $order);
        return $this->httpClient
            ->setJwt()
            ->setQuery($data)
            ->setMethod('POST')
            ->setUri(route('payment.pay', '', false))
            ->send()
            ->getBody()
            ->getContents();
    }

    /**
     * @param array{order_id: int, payout_token:string, price:int} $data
     * @param OrderPerformer $order
     * @return void
     */
    public function payout(array $data, OrderPerformer $order): void
    {
        Gate::authorize('payout', $order);
        $this->httpClient
            ->setJwt()
            ->setQuery($data)
            ->setMethod('POST')
            ->setUri(route('payment.payout', '', false))
            ->send();
    }

    /**
     * @param array{order_id: int, pay_id:string, price:int} $data
     * @param Order $order
     * @return void
     */
    public function refund(array $data, Order $order): void
    {
        Gate::authorize('refund', $order);
        $this->httpClient
            ->setJwt()
            ->setQuery($data)
            ->setMethod('POST')
            ->setUri(route('payment.refund', '', false))
            ->send();
    }
}
