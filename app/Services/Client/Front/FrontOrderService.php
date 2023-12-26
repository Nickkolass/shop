<?php

namespace App\Services\Client\Front;

use App\Components\HttpClient\HttpClientInterface;

class FrontOrderService
{

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function index(array $data): array
    {
        $orders = $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.orders.index', '', false))
            ->setQuery($data)
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
        return json_decode($orders, true);

    }

    public function store(array $data): string
    {
        $pay_url = $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.orders.store', '', false))
            ->setQuery($data)
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
        session()->forget(['cart', 'filter', 'paginate']);
        return $pay_url;
    }

    public function show(int $order_id): array
    {
        $order = $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.orders.show', $order_id, false))
            ->setMethod('POST')
            ->send()
            ->getBody()
            ->getContents();
        return json_decode($order, true);
    }

    public function update(int $order_id): void
    {
        $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.orders.update', $order_id, false))
            ->setMethod('PATCH')
            ->send();
    }

    public function destroy(int $order_id): void
    {
        $this->httpClient
            ->setJwt()
            ->setUri(route('back.api.orders.destroy', $order_id, false))
            ->setQuery(['due_to_pay' => request()->input('due_to_pay', false)])
            ->setMethod('DELETE')
            ->send();
    }

    public function destroyOrderPerformer(int $orderPerformer_id): void
    {
        $this->httpClient
            ->setJwt()
            ->setMethod('DELETE')
            ->setUri(route('back.api.orders.destroyOrderPerformer', $orderPerformer_id, false))
            ->send();
    }
}
