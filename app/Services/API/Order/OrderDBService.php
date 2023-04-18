<?php

namespace App\Services\API\Order;

use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderDBService
{
    public function store($data)
    {

        DB::beginTransaction();
        try {
            $this->productCountUpdate($data['cart']);
            $order_id = $this->orderStore($data);
            $data['cart'] = collect($data['cart'])->groupBy('saler_id');
            $this->orderPerformerStore($data, $order_id);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    public function update(Order $order)
    {
        DB::beginTransaction();
        try {
            $order->update(['status' => 'Получен ' . now()]);
            $order->orderPerformers()->update(['status' => 'Получен ' . now()]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    public function delete(Order $order)
    {
        DB::beginTransaction();
        try {
            $order->orderPerformers()->update(['status' => 'Отменен ' . now()]);
            $order->orderPerformers()->delete();
            $order->update(['status' => 'Отменен ' . now()]);
            $order->delete();
            //возврат денег
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $exception->getMessage();
        }
    }


    private function productCountUpdate(&$cart)
    {
        $products = Product::select('id', 'count', 'saler_id', 'price')->find(array_column($cart, 'product_id'));

        foreach ($cart as &$order) {
            $product = $products->where('id', $order['product_id'])->first();

            $order['price'] = $order['amount'] * $product->price;
            $order['saler_id'] = $product->saler_id;
            if ($order['cart_id']) unset($order['cart_id']);

            $upd['count'] = $product->count - $order['amount'];
            $upd['count'] <= 0 ? $upd['is_published'] = 0 : '';
            $product->update($upd);
        }
    }


    private function orderStore($data)
    {
        return Order::create([
            'user_id' => $data['user_id'],
            'products' => json_encode($data['cart']),
            'delivery' => $data['delivery'],
            'total_price' => $data['total_price'],
            'payment' => $data['payment'],
            'payment_status' => $data['payment_status'],
        ])->id;
    }


    private function orderPerformerStore($data, $order_id)
    {
        foreach ($data['cart'] as $saler_id => $order) {
            OrderPerformer::create([
                'saler_id' => $saler_id,
                'user_id' => $data['user_id'],
                'order_id' => $order_id,
                'products' => json_encode($order),
                'dispatch_time' => now()->addDays(25),
                'delivery' => $data['delivery'],
                'total_price' => $order->pluck('price')->sum(),
            ]);
        }
    }
}
