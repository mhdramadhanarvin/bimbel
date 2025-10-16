<?php

namespace Database\Factories;

use App\Enums\UserGenderEnum;
use App\Enums\UserProgramEnum;
use App\Enums\UserReligionEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function student(): static
    {
        return $this->state(fn(array $attributes) => [
            'gender' => $this->faker->randomElement(UserGenderEnum::cases())->value,
            'place_of_birth' => $this->faker->city(),
            'date_of_birth' => $this->faker->date('Y-m-d'),  // Format as string
            'religion' => $this->faker->randomElement(UserReligionEnum::cases())->value,
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
            'programme' => $this->faker->randomElement(UserProgramEnum::cases())->value,
            'origin_school' => $this->faker->company() . ' School',  // Random school name
            'parent_name' => $this->faker->name(),
            'parent_phone_number' => $this->faker->phoneNumber(),
            'parent_address' => $this->faker->address(),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}
