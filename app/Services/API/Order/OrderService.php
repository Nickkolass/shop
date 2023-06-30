<?php

namespace App\Services\API\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

class OrderService
{

    private $service;

    public function __construct(OrderProductService $service)
    {
        $this->service = $service;
    }

    public function index(int $user_id, int $page): ?Paginator
    {
        $user = User::select('id', 'role')->find($user_id);
        $orders = $user->isAdmin() ?  Order::query() : $user->orders();
        $orders = $orders->without('payment_status', 'payment')->with(['orderPerformers' => function ($q) {
            $q->withTrashed()->select('order_id', 'dispatch_time');
        }])->latest()->withTrashed()->simplePaginate(3, ['*'], 'page', $page)->withPath('');
        if($orders->count() != 0) return $this->service->getProductsForIndex($orders);
        return null;
    }


    public function show(Order $order): Order
    {
        $order->load(['orderPerformers' => function ($q) {
            $q->select('id', 'saler_id', 'order_id', 'status', 'dispatch_time')->withTrashed();
        }]);

        return $this->service->getProductsForShow($order);
    }
}
