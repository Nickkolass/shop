<?php

namespace App\Services\Admin\OrderPerformer;

use App\Events\Order\OrderCanceled;
use App\Events\Order\OrderPerformerCanceled;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OrderPerformerService
{

    public function __construct(private readonly OrderPerformerProductService $service)
    {
    }

    public function index(): Paginator
    {
        $orders = OrderPerformer::query()
            ->withTrashed()
            ->whereNot('status', OrderPerformer::STATUS_WAIT_PAYMENT)
            ->when(Gate::check('role', [User::class, User::ROLE_ADMIN]),
                fn(Builder $q) => $q->with('user:id,name'),
                fn(Builder $q) => $q->whereHas('saler', fn(Builder $b) => $b->where('id', session('user.id'))))
            ->with('saler:id,name')
            ->latest('created_at')
            ->simplePaginate(5);

        $this->service->getProductsForIndex($orders);
        return $orders;
    }

    public function show(OrderPerformer $order): void
    {
        $order->load('saler:users.id,name', 'order:id,status');
        $this->service->getProductsForShow($order);
    }

    public function update(OrderPerformer $order): void
    {
        $order->increment('status');
    }

    public function delete(OrderPerformer $order, bool $canceler_is_client = false): void
    {
        DB::beginTransaction();
        $order_has_deleted = $this->countProductRestoration((array)$order->productTypes)->deleteOrder($order);
        event($order_has_deleted
            ? new OrderCanceled($order->order()->withTrashed()->first(), [$order->id])
            : new OrderPerformerCanceled($order, $canceler_is_client));
        DB::commit();
    }

    /**
     * @param array<array{productType_id:int, saler_id:int, amount:int, price:int}> $productTypes
     * @return self
     */
    private function countProductRestoration(array $productTypes): self
    {
        $productTypes = array_column($productTypes, 'amount', 'productType_id');
        $type_upd = ProductType::query()
            ->whereIn('id', array_keys($productTypes))
            ->pluck('count', 'id')
            ->transform(function (int $count, int $id) use ($productTypes) {
                return ['id' => $id, 'count' => $count + (int)$productTypes[$id], 'is_published' => true];
            })
            ->all();
        ProductType::upsert($type_upd, 'id');
        return $this;
    }

    private function deleteOrder(OrderPerformer $order): bool
    {
        $order->update(['status' => OrderPerformer::STATUS_CANCELED, 'deleted_at' => now()]);
        return (bool)$order->order()->doesntHave('orderPerformers')->update(['status' => Order::STATUS_CANCELED, 'deleted_at' => now()]);
    }
}
