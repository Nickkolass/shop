<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductType>
 */
class ProductTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $product_id = Product::query()->take(1)->latest('id')->pluck('id')['0'];
        return [
            'price' => $this->faker->numberBetween(1, 10000),
            'count' => $this->faker->numberBetween(1, 10),
            'is_published' => 1,
            'preview_image' => '',
            'product_id' => $product_id,
        ];
    }
}
