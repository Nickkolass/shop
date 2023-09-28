<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->unique()->sentence(5),
            'description' => $this->faker->realText(),
            'category_id' => Category::query()->take(1)->latest('id')->pluck('id')['0'],
            'saler_id' => User::query()->take(1)->latest('id')->pluck('id')['0'],
        ];
    }
}
