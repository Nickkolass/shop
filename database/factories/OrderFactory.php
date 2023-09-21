<?php

namespace Database\Factories;

use App\Models\ProductType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
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
        $user = User::inRandomOrder()->select('id', 'address', 'card')->first();
        $productTypes = ProductType::query()
            ->inRandomOrder()
            ->take(4)
            ->with('product:id,saler_id')
            ->select('id', 'product_id', 'price')
            ->get()
            ->map(function (ProductType $productType) {
                return [
                    'productType_id' => $productType->id,
                    'amount' => $amount = random_int(1, 3),
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
            'payment' => (string) $user->card,
            'payment_status' => true,
        ];
    }
}
