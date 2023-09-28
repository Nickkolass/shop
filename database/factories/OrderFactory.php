<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::query()->inRandomOrder()->select('id', 'address', 'card')->first();
        $productTypes = ProductType::query()
            ->with('product:id,saler_id')
            ->select('id', 'product_id', 'price')
            ->take(4)
            ->inRandomOrder()
            ->get()
            ->map(function (ProductType $productType) {
                return [
                    'productType_id' => $productType->id,
                    'amount' => $amount = rand(1, 3),
                    'price' => $productType->price * $amount,
                    'saler_id' => $productType->product->saler_id,
                ];
            });

        cache()->put('saler_ids_for_factory_order_performers', $productTypes->pluck('saler_id')->unique()->values());
        cache()->put('current_saler_id_for_factory_order_performers', 0);

        return [
            'user_id' => $user->id,
            'productTypes' => $productTypes->all(),
            'delivery' => $user->address,
            'total_price' => $productTypes->pluck('price')->sum(),
            'payment' => (string)$user->card,
            'payment_status' => true,
        ];
    }
}
