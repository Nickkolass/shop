<?php

namespace Database\Factories;

use App\Models\User;
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
            'role' => User::ROLE_SALER,
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make($this->faker->password()),
            'name' => $this->faker->name(),
            'surname' => $this->faker->lastName(),
            'patronymic' => $this->faker->name(),
            'gender' => $this->faker->boolean(),
            'age' => $this->faker->numberBetween(20, 70),

            'postcode' => (int)$this->faker->postcode(),
            'address' => $this->faker->address(),

            'INN' => (int)$this->faker->unique()->numerify('#########'),
            'registredOffice' => $this->faker->address(),
            'card' => [
                'payout_token' => 'oam6D9csiLcfw7fA9j2C_rj-.SC.001.202310',
                'first6' => '555555',
                'last4' => '4477',
                'card_type' => 'MasterCard',
                'issuer_country' => 'US',
            ],
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
