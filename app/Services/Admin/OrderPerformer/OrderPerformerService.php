<?php

namespace App\Services\Admin\OrderPerformer;

use App\Components\Method;
use App\Mail\MailOrderPerformerDestroy;
use App\Models\OrderPerformer;
use App\Models\ProductType;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


class OrderPerformerService
{

    public OrderPerformerProductService $service;

    public function __construct(OrderPerformerProductService $service)
    {
        $this->service = $service;
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

    public function update(OrderPerformer $order): ?string
    {
        DB::beginTransaction();
        try {

            $order->update(['status' => 'Отправлен ' . now()]);
            $order->order()
                ->whereHas('orderPerformers', function ($q) use ($order) {
                    $q->where('order_id', $order->order_id)->where('status', '!=', 'В работе');
                })
                ->update(['status' => 'Отправлен']);

            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }

    public function delete(OrderPerformer $order): ?string
    {
        DB::beginTransaction();
        try {

            $order->update(['status' => 'Отменен ' . now()]);
            $order->delete();
            $query = $order->order()->doesntHave('orderPerformers');
            $query->update(['status' => 'Отменен ' . now()]);
            $query->delete();
            Mail::to($order->user()->pluck('email'))->send(new MailOrderPerformerDestroy($order));

            DB::commit();
            return null;
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }
}
