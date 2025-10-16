<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPayment>
 */
class UserPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'registration_number' => $this->faker->unique()->regexify('[A-Z]{3}[0-9]{6}'),  // Example: ABC123456
            'proof_of_payment' => $this->faker->filePath(),  // Generates a fake file path
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'rejected']),
            'is_notify' => $this->faker->boolean(),
        ];
    }
}
