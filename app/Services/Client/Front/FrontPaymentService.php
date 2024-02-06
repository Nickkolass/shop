<?php

namespace App\Services\Client\Front;

use App\Components\Transport\Protokol\Http\HttpClientInterface;

class FrontPaymentService
{

    public function __construct(private readonly HttpClientInterface $transportService)
    {
    }

    public function pay(int $order_id): string
    {
        return $this->transportService
            ->setJwt()
            ->setQuery(['return_url' => route('client.orders.index')])
            ->setMethod('POST')
            ->setUri(route('back.api.orders.pay', $order_id, false))
            ->send()
            ->getBody()
            ->getContents();
    }

    public function refund(int $order_id): void
    {
        $this->transportService
            ->setJwt()
            ->setMethod('POST')
            ->setUri(route('back.api.orders.refund', $order_id, false))
            ->publish();
    }
}
