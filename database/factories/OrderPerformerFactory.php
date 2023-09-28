<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderPerformer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Cache;

/**
 * @extends Factory<OrderPerformer>
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
        $currentSaler_id = Cache::get('saler_ids_for_factory_order_performers')[Cache::get('current_saler_id_for_factory_order_performers')];
        $order = Order::query()->latest('id')->first();
        $productTypes = collect($order->productTypes)->groupBy('saler_id')[$currentSaler_id];

        Cache::increment('current_saler_id_for_factory_order_performers');

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
