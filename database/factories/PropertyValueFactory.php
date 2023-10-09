<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyValueFactory extends Factory
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
            'property_id' => Property::query()->latest('id')->first()->id,
        ];
    }
}
