<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
            'category_id' => Category::take(1)->latest('id')->pluck('id')['0'],
            'saler_id' => User::take(1)->latest('id')->pluck('id')['0'],
        ];
    }
}
