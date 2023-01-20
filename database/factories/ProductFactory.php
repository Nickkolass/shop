<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=Product>
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
            'title' => $this->faker->sentence(5),
            'description' => $this->faker->text(),
            'content' => $this->faker->text(),
            'preview_image' => '',
            'price' => $this->faker->numberBetween(0, 3000),
            'count' => $this->faker->numberBetween(1, 20),
            'is_published' => $this->faker->boolean(),
            'category_id' => Category::get()->random()->id,
            'group_id' => Group::get()->random()->id,
            'saler_id' => User::get()->random()->id,
        ];
    }
}
