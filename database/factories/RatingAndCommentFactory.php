<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\RatingAndComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RatingAndComment>
 */
class RatingAndCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'rating' => $this->faker->numberBetween(1, 5),
            'message' => $this->faker->realText(),
            'user_id' => User::query()->take(1)->latest('id')->pluck('id')['0'],
            'product_id' => Product::query()->take(1)->latest('id')->pluck('id')['0'],
        ];
    }
}
