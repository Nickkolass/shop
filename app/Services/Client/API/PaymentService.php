<?php

namespace App\Services\Client\API;

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
        return $this->httpClient
            ->setQuery($data)
            ->setMethod('POST')
            ->setUri('host.docker.internal:8877/api/payment/pay')
            ->send()
            ->getBody()
            ->getContents();
    }

    /**
     * @param array{order_id: int, payout_token:string, price:int} $data
     * @param OrderPerformer $order
     * @param bool $is_event
     * @return void
     */
    public function payout(array $data, OrderPerformer $order, bool $is_event = false): void
    {
        Gate::forUser($is_event ? $order->saler()->first('id') : auth('api')->user())
            ->authorize('payout', $order);
        $this->httpClient
            ->setQuery($data)
            ->setMethod('POST')
            ->setUri('host.docker.internal:8877/api/payment/payout')
            ->send();
    }

    /**
     * @param array{order_id: int, pay_id:string, price:int} $data
     * @param Order $order
     * @param bool $is_event
     * @return void
     */
    public function refund(array $data, Order $order, bool $is_event = false): void
    {
        dump(2);
        Gate::forUser($is_event ? $order->user()->first() : auth('api')->user())
            ->authorize('refund', $order);
        $this->httpClient
            ->setQuery($data)
            ->setMethod('POST')
            ->setUri('host.docker.internal:8877/api/payment/refund')
            ->send();
    }
}
