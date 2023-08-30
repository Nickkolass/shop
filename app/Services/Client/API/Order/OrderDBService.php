<?php

namespace App\Services\Client\API\Order;

use App\Events\OrderStored;
use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\ProductType;
use Illuminate\Support\Facades\DB;

class OrderDBService
{
    public function store(array $data): void
    {
        DB::beginTransaction();
        $this->productCountUpdate($data['cart'])->orderStore($data)->orderPerformerStore($data);
        DB::commit();
    }

    public function update(Order $order): void
    {
        DB::beginTransaction();
        $order->update(['status' => 'Получен ' . now()]);
        $order->orderPerformers()->update(['status' => 'Получен ' . now()]);
        DB::commit();
    }

    public function delete(Order $order): void
    {
        DB::beginTransaction();
        $order->orderPerformers()->update(['status' => 'Отменен ' . now()]);
        $order->orderPerformers()->delete();
        $order->update(['status' => 'Отменен ' . now()]);
        $order->delete();
        DB::commit();
    }

    private function productCountUpdate(array &$cart): OrderDBService
    {
        $productTypes = ProductType::query()
            ->select('id', 'count', 'price', 'product_id', 'is_published')
            ->with('product:id,saler_id')
            ->find(array_keys($cart));

        foreach ($cart as $productType_id => $amount) {
            $productType = $productTypes->where('id', $productType_id)->first();

            $update[$productType_id]['id'] = $productType->id;
            $update[$productType_id]['count'] = $productType->count - $amount;
            $update[$productType_id]['is_published'] = $update[$productType_id]['count'] > 0 ? $productType->is_published : 0;

            $cart[$productType_id] = [
                'productType_id' => $productType_id,
                'amount' => $amount,
                'price' => $productType->price * $amount,
                'saler_id' => $productType->product->saler_id,
            ];
        }
        ProductType::upsert($update, ['id'], ['count', 'is_published']);
        return $this;
    }

    private function orderStore(array &$data): OrderDBService
    {
        $data['order'] = Order::create([
            'user_id' => $data['user_id'],
            'productTypes' => $data['cart'],
            'delivery' => $data['delivery'],
            'total_price' => $data['total_price'],
            'payment' => $data['payment'],
            'payment_status' => $data['payment_status'],
        ]);
        return $this;
    }

    private function orderPerformerStore(array &$data): OrderDBService
    {
        $data['cart'] = collect($data['cart'])
            ->groupBy('saler_id')
            ->map(function (iterable $order, int $saler_id) use ($data) {
                return [
                    'order_id' => $data['order']->id,
                    'saler_id' => $saler_id,
                    'user_id' => $data['user_id'],
                    'productTypes' => $order,
                    'dispatch_time' => now()->addDays(25),
                    'delivery' => $data['delivery'],
                    'total_price' => $order->sum('price'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });
        OrderPerformer::insert($data['cart']->all());

        event(new OrderStored($data['cart']));

        return $this;
    }
}