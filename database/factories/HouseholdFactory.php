<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Household>
 */
class HouseholdFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->lastName().' porodica',
            'owner_id' => User::factory(),
            'default_currency' => 'BAM',
            'max_members' => 5,
        ];
    }
}
