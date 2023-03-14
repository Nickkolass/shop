<?php

namespace Database\Factories;

use App\Models\Color;
use App\Models\Group;
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
            'price' => $this->faker->numberBetween(1, 3000),
            'count' => $this->faker->numberBetween(0, 20),
            'is_published' => 1,
            'color_id' => random_int(1,Color::count()),
            'category_id' => Group::latest('id')->first()->category_id,
            'group_id' => Group::latest('id')->first()->id,
            'saler_id' => Group::latest('id')->first()->saler_id,
        ];
    }
}
