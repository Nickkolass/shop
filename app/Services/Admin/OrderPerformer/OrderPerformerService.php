<?php

namespace App\Services\Admin\OrderPerformer;

use App\Events\Order\OrderPerformerCanceled;
use App\Models\OrderPerformer;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class OrderPerformerService
{

    public function __construct(private readonly OrderPerformerProductService $service)
    {
    }

    public function index(): Paginator
    {
        $user = session('user');
        $orders = OrderPerformer::query()
            ->withTrashed()
            ->when($user['role'] == User::ROLE_ADMIN,
                fn(Builder $q) => $q->with('user:id,name'),
                fn(Builder $q) => $q->whereHas('saler', fn(Builder $b) => $b->where('id', $user['id'])))
            ->with('saler:id,name')
            ->latest('created_at')
            ->simplePaginate(5);

        $this->service->getProductsForIndex($orders);
        return $orders;
    }

    public function show(OrderPerformer $order): void
    {
        $order->load('saler:users.id,name');
        $this->service->getProductsForShow($order);
    }

    public function update(OrderPerformer $order): void
    {
        DB::beginTransaction();
        $order->update(['status' => 'Отправлен ' . now()]);
        $order->order()
            ->whereHas('orderPerformers', function (Builder $q) use ($order) {
                $q->where('order_id', $order->order_id)->where('status', '!=', 'В работе');
            })
            ->update(['status' => 'Отправлен']);
        DB::commit();
    }

    public function delete(OrderPerformer $order): void
    {
        $now = now();
        DB::beginTransaction();
        foreach ($order->productTypes as $type) {
            ProductType::query()->where('id', $type['productType_id'])->increment('count', $type['amount']);
        }
        $order->update(['status' => 'Отменен ' . $now, 'deleted_at' => $now]);
        $order->order()->doesntHave('orderPerformers')->update(['status' => 'Отменен ' . $now, 'deleted_at' => $now]);
        event(new OrderPerformerCanceled($order, false));
        DB::commit();
    }
}
