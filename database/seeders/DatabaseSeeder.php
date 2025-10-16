<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPayment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
        ]);

        User::factory(10)->has(UserPayment::factory())->student()->create();
    }
}
