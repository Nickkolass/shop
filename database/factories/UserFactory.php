<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<mixed>
     */
    public function definition(): array
    {
        return [
            'role' => 2,
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make($this->faker->password(5)),
            'name' => $this->faker->name(),
            'surname' => $this->faker->lastName(),
            'patronymic' => $this->faker->name(),
            'gender' => $this->faker->numberBetween(1, 2),
            'age' => $this->faker->numberBetween(20, 70),

            'card' => $this->faker->numerify('################'),
            'postcode' => (int)$this->faker->postcode(),
            'address' => $this->faker->address(),

            'INN' => (int)$this->faker->unique()->numerify('#########'),
            'registredOffice' => $this->faker->address(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
