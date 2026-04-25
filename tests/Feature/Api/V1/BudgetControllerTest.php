<?php

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

it('returns the updated budget payload after saving', function () {
    $user = User::factory()->withHousehold()->onTrial()->create();
    $category = Category::factory()->create([
        'name' => 'groceries',
        'icon' => 'basket',
        'color' => '#10B981',
    ]);
    $month = Carbon::now()->startOfMonth()->toDateString();

    Sanctum::actingAs($user);

    $response = $this->postJson('/api/v1/budgets', [
        'month' => $month,
        'overall_amount' => 2000,
        'categories' => [
            ['category_id' => $category->id, 'amount' => 500],
        ],
    ])->assertSuccessful();

    $response
        ->assertJsonPath('message', 'Budget saved successfully.')
        ->assertJsonPath('data.month', $month)
        ->assertJsonPath('data.overall_budget', 2000)
        ->assertJsonPath('data.category_budgets.0.category_id', $category->id)
        ->assertJsonPath('data.category_budgets.0.category_name', 'groceries')
        ->assertJsonPath('data.category_budgets.0.amount', 500)
        ->assertJsonPath('data.currency', $user->household->default_currency);
});

it('returns budget data for the requested month', function () {
    $user = User::factory()->withHousehold()->onTrial()->create();
    $category = Category::factory()->create();
    $month = Carbon::now()->startOfMonth()->toDateString();

    Budget::factory()->create([
        'household_id' => $user->household_id,
        'category_id' => null,
        'month' => $month,
        'amount' => 1800,
    ]);

    Budget::factory()->create([
        'household_id' => $user->household_id,
        'category_id' => $category->id,
        'month' => $month,
        'amount' => 300,
    ]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/budgets?month='.$month)->assertSuccessful();

    $response
        ->assertJsonPath('data.month', $month)
        ->assertJsonPath('data.overall_budget', 1800)
        ->assertJsonPath('data.category_budgets.0.category_id', $category->id)
        ->assertJsonPath('data.category_budgets.0.amount', 300);
});
