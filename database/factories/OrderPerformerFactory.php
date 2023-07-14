<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderPerformer>
 */
class OrderPerformerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $current = cache()->get('factoryCurrentOrderSaler');
        $currentSaler_id = cache()->get('factoryOrders')[$current];
        $order = Order::latest('id')->first();

        $productTypes = collect(json_decode($order->productTypes))->groupBy('saler_id')[$currentSaler_id];

        return [
            'order_id' => $order->id,
            'saler_id' => $currentSaler_id,
            'user_id' => $order->user_id,
            'productTypes' => json_encode($productTypes),
            'dispatch_time' => $order->created_at->addDays(25),
            'delivery' => $order->delivery,
            'total_price' => $productTypes->sum('price'),
        ];
    }
}
