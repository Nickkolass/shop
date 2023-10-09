<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->sentence(5),
            'description' => $this->faker->realText(),
            'category_id' => Category::query()->take(1)->latest('id')->pluck('id')['0'],
            'saler_id' => User::query()->take(1)->latest('id')->pluck('id')['0'],
        ];
    }
}
