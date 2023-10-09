<?php

namespace Database\Factories;

use App\Models\Option;
use Illuminate\Database\Eloquent\Factories\Factory;

class OptionValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<mixed>
     */
    public function definition(): array
    {
        return [
            'value' => $this->faker->unique()->word(),
            'option_id' => Option::query()->take(1)->latest('id')->pluck('id')[0],
        ];
    }
}
