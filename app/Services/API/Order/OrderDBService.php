<?php

namespace App\Services\API\Order;

use App\Models\Order;
use App\Models\OrderPerformer;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderDBService
{
    public function store($data)
    {
        DB::beginTransaction();
        try {
            $this->productCountUpdate($data['cart'])->orderStore($data)->orderPerformerStore($data);
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
        $productType_ids = array_keys($cart);
        $productTypes = ProductType::select('id', 'count', 'price', 'product_id')->with('product:id,saler_id')->find($productType_ids);

        foreach ($cart as $productType_id => $amount) {
            $pT = $productTypes->where('id', $productType_id)->first();
            $pT->count -= $amount;
            $pT->count > 0 ?: $pT->is_published = 0;
            $pT->update();
            $cart[$productType_id] = [
                'productType_id' => $productType_id,
                'amount' => $amount,
                'price' => $pT->price * $amount,
                'saler_id' => $pT->product->saler_id,
            ];
        }
        return $this;
      
    }


    private function orderStore(&$data)
    {
        $data['order_id'] = Order::create([
            'user_id' => $data['user_id'],
            'productTypes' => json_encode($data['cart']),
            'delivery' => $data['delivery'],
            'total_price' => $data['total_price'],
            'payment' => $data['payment'],
            'payment_status' => $data['payment_status'],
        ])->id;
        return $this;
    }


    private function orderPerformerStore($data)
    {
        $data['cart'] = collect($data['cart'])->groupBy('saler_id');

        foreach ($data['cart'] as $saler_id => $order) {
            OrderPerformer::create([
                'saler_id' => $saler_id,
                'user_id' => $data['user_id'],
                'order_id' => $data['order_id'],
                'productTypes' => json_encode($order),
                'dispatch_time' => now()->addDays(25),
                'delivery' => $data['delivery'],
                'total_price' => $order->sum('price'),
            ]);
        }
        return $this;
    }
}
