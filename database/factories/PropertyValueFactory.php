<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\PropertyValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyValue>
 */
class PropertyValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'value' => $this->faker->unique()->word(),
            'property_id' => Property::query()->latest('id')->first()->id,
        ];
    }
}
