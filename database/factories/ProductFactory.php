<?php

namespace Database\Factories;

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
        $group = Group::latest('id')->first();
        return [
            'title' => $this->faker->sentence(5),
            'description' => $this->faker->realText(),
            'price' => $this->faker->numberBetween(1, 10000),
            'count' => $this->faker->numberBetween(1, 10),
            'is_published' => 1,
            'preview_image' => '',
            'category_id' => $group->category_id,
            'group_id' => $group->id,
            'saler_id' => $group->saler_id,
        ];
    }
}
