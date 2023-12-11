<?php

namespace App\Services\Client\Front;

use App\Components\HttpClient\HttpClientInterface;

class FrontPaymentService
{

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    /**
     * @param array{order_id: int, price: int} $data
     * @return string
     */
    public function pay(array $data): string
    {
        return $this->httpClient
            ->setJwt()
            ->setQuery($data)
            ->setMethod('POST')
            ->setUri(route('back.api.orders.pay', $data['order_id'], false))
            ->send()
            ->getBody()
            ->getContents();
    }

    /**
     * @param array{order_id: int, pay_id:string, price:int} $data
     * @return void
     */
    public function refund(array $data): void
    {
        $this->httpClient
            ->setJwt()
            ->setQuery($data)
            ->setMethod('POST')
            ->setUri(route('back.api.orders.refund', $data['order_id'], false))
            ->send();
    }
}
