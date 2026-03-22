<?php

namespace Database\Seeders;

use App\Models\Household;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $household = Household::create([
            'name' => 'Test Household',
            'owner_id' => $user->id,
            'default_currency' => $user->default_currency,
        ]);

        $user->update(['household_id' => $household->id]);
    }
}
