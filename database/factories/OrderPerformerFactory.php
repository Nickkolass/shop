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
        $currentSaler_id = cache('saler_ids_for_factory_order_performers')[cache('current_saler_id_for_factory_order_performers')];
        $order = Order::latest('id')->first();
        $productTypes = collect($order->productTypes)->groupBy('saler_id')[$currentSaler_id];

        cache()->increment('current_saler_id_for_factory_order_performers');

        return [
            'order_id' => $order->id,
            'saler_id' => $currentSaler_id,
            'user_id' => $order->user_id,
            'productTypes' => $productTypes,
            'dispatch_time' => $order->created_at->addDays(25),
            'delivery' => $order->delivery,
            'total_price' => $productTypes->sum('price'),
        ];
    }
}
