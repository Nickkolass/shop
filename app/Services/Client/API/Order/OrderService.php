<?php

namespace App\Services\Client\API\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;

class OrderService
{

    public function __construct(private readonly OrderProductService $service)
    {
    }

    public function index(int $page): ?Paginator
    {
        $orders = Order::query()
            ->withTrashed()
            ->when(!Gate::check('role', [User::class, User::ROLE_ADMIN]),
                fn(Builder $q) => $q->where('user_id', auth('api')->id()))
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

    public function show(Order $order): void
    {
        $order->load(['orderPerformers' => function (Builder $q) {
            /** @phpstan-ignore-next-line */
            $q->select('id', 'saler_id', 'order_id', 'total_price', 'productTypes', 'status', 'dispatch_time')
                ->selectSub(function (\Illuminate\Contracts\Database\Query\Builder $q) {
                    $q->from('users')
                        ->whereColumn('users.id', 'saler_id')
                        ->select('name');
                }, 'saler_name')
                ->withTrashed();
        }])
            ->setAttribute('refundable', Gate::check('refund', $order))
            ->setAttribute('cancelable', Gate::check('delete', $order));

        $this->service->getProductsForShow2($order);
    }
}
