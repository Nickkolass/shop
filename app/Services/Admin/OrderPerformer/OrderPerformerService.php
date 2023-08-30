<?php

namespace App\Services\Admin\OrderPerformer;

use App\Mail\MailOrderPerformerDestroy;
use App\Models\OrderPerformer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderPerformerService
{

    public function __construct(private readonly OrderPerformerProductService $service)
    {
    }

    public function index(): Paginator
    {
        $user = session('user');
        $orders = OrderPerformer::query()
            ->when($user['role'] == 'admin',
                fn($q) => $q->with('user:id,name'),
                fn($q) => $q->whereHas('saler', function ($b) use ($user) {
                    $b->where('id', $user['id']);
                }))
            ->withTrashed()
            ->latest('created_at')
            ->with('saler:id,name')
            ->simplePaginate(5);

        $this->service->getProductsForIndex($orders);
        return $orders;
    }

    public function show(OrderPerformer &$order): void
    {
        $order->load('saler:users.id,name');
        $this->service->getProductsForShow($order);
    }

    public function update(OrderPerformer $order): void
    {
        DB::beginTransaction();
        $order->update(['status' => 'Отправлен ' . now()]);
        $order->order()
            ->whereHas('orderPerformers', function ($q) use ($order) {
                $q->where('order_id', $order->order_id)->where('status', '!=', 'В работе');
            })
            ->update(['status' => 'Отправлен']);
        DB::commit();
    }

    public function delete(OrderPerformer $order): void
    {
        DB::beginTransaction();
        $order->update(['status' => 'Отменен ' . now()]);
        $order->delete();
        $query = $order->order()->doesntHave('orderPerformers');
        $query->update(['status' => 'Отменен ' . now()]);
        $query->delete();
        Mail::to($order->user()->pluck('email'))->send(new MailOrderPerformerDestroy($order));
        DB::commit();
    }
}
