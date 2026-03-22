<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Household;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'household_id' => Household::factory(),
            'category_id' => Category::factory(),
            'amount' => fake()->randomFloat(2, 1, 500),
            'currency' => 'BAM',
            'merchant' => fake()->optional()->company(),
            'description' => fake()->sentence(),
            'original_text' => null,
            'expense_date' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
        ];
    }
}
