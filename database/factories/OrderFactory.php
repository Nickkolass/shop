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
        $productTypes = ProductType::inRandomOrder()->take(4)->with('product:id,saler_id')->select('id', 'product_id', 'price')->get()->map(function(ProductType $pT) {
            return [
                'productType_id' => $pT->id,
                'amount' => $amount = random_int(1,3),
                'price' => $pT->price * $amount,
                'saler_id' => $pT->product->saler_id,
            ];
        });

        cache()->put('factoryOrders', $productTypes->pluck('saler_id')->unique()->values(), 60);

        return [
            'user_id' => User::inRandomOrder()->first('id')->id,
            'productTypes' => json_encode($productTypes),
            'delivery' => $this->faker->address(),
            'total_price' => $productTypes->pluck('price')->sum(),
            'payment' => 'card',
            'payment_status' => true,
        ];
    }
}
