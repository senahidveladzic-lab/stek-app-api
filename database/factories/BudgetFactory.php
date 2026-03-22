<?php

namespace Database\Factories;

use App\Models\Household;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'household_id' => Household::factory(),
            'category_id' => null,
            'month' => Carbon::now()->startOfMonth()->format('Y-m-d'),
            'amount' => fake()->randomFloat(2, 100, 5000),
        ];
    }
}
