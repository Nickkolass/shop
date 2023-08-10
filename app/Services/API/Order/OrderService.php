<?php

namespace App\Services\API\Order;

use App\Models\Order;
use Illuminate\Contracts\Pagination\Paginator;

class OrderService
{

    private OrderProductService $service;

    public function __construct(OrderProductService $service)
    {
        $this->service = $service;
    }

    public function index(int $page): ?Paginator
    {
        $user = auth('api')->user();

        $orders = Order::query()
            ->when(!$user->isAdmin(), function ($q) use ($user) {
                return $user->orders();
            })
            ->without('payment_status', 'payment')
            ->with(['orderPerformers' => function ($q) {
                $q->withTrashed()->select('order_id', 'dispatch_time');
            }])
            ->latest()
            ->withTrashed()
            ->simplePaginate(3, ['*'], 'page', $page)
            ->withPath('');
        if ($orders->count() != 0) {
            $this->service->getProductsForIndex($orders);
            return $orders;
        }
        return null;
    }

    public function show(Order $order): Order
    {
        $order->load(['orderPerformers' => function ($q) {
            $q->select('id', 'saler_id', 'order_id', 'status', 'dispatch_time')->withTrashed();
        }]);
        $this->service->getProductsForShow($order);
        return $order;
    }
}
