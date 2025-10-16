<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

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
            'registration_number' => fake()->unique()->regexify('[A-Z]{3}[0-9]{6}'),
            'proof_of_payment' => $this->copyingFile(fake()->randomElement(['200x200.png', '250x250.png'])),
            'status' => fake()->randomElement(['pending', 'confirmed', 'rejected']),
            'is_notify' => fake()->boolean(),
        ];
    }

    private function copyingFile($filename)
    {
        $sourcePath = public_path('images/' . $filename);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $newFilename = uniqid() . '.' . $extension;
        $destinationPath = Storage::disk('public')->path('proof_of_payment/' . $newFilename);

        $destinationDir = dirname($destinationPath);
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        if (file_exists($sourcePath)) {
            copy($sourcePath, $destinationPath);
        }

        return '/proof_of_payment/' . $newFilename;
    }
}
