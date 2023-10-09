<?php

namespace App\Services\Client\API\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;

class OrderService
{

    public function __construct(private readonly OrderProductService $service)
    {
    }

    public function index(int $page): ?Paginator
    {
        $user = auth('api')->user();
        /** @var User $user */

        $orders = Order::query()
            ->withTrashed()
            ->when(!$user->isAdmin(), fn(Builder $q) => $q->where('user_id', $user->id))
            ->with(['orderPerformers' => function (Builder $q) {
                /** @phpstan-ignore-next-line */
                $q->withTrashed()->select('order_id', 'dispatch_time');
            }])
            ->latest()
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
        $order->load(['orderPerformers' => function (Builder $q) {
            /** @phpstan-ignore-next-line */
            $q->withTrashed()->select('id', 'saler_id', 'order_id', 'status', 'dispatch_time');
        }]);
        $this->service->getProductsForShow($order);
        return $order;
    }
}
