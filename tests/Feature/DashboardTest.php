<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('dashboard summary includes member_spending', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $category = Category::factory()->create();
    Expense::factory()->create([
        'user_id' => $user->id,
        'household_id' => $user->household_id,
        'category_id' => $category->id,
        'amount' => 50.00,
        'expense_date' => Carbon::now()->format('Y-m-d'),
    ]);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('dashboard')
        ->has('summary.member_spending', 1)
        ->where('summary.member_spending.0.user_id', $user->id)
    );
});

test('dashboard summary includes previous_month_same_period_total', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $category = Category::factory()->create();
    Expense::factory()->create([
        'user_id' => $user->id,
        'household_id' => $user->household_id,
        'category_id' => $category->id,
        'amount' => 100.00,
        'expense_date' => Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d'),
    ]);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('dashboard')
        ->has('summary.previous_month_same_period_total')
    );
});

test('by_category items include previous_total', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $category = Category::factory()->create();

    Expense::factory()->create([
        'user_id' => $user->id,
        'household_id' => $user->household_id,
        'category_id' => $category->id,
        'amount' => 75.00,
        'expense_date' => Carbon::now()->subMonth()->format('Y-m-d'),
    ]);

    Expense::factory()->create([
        'user_id' => $user->id,
        'household_id' => $user->household_id,
        'category_id' => $category->id,
        'amount' => 50.00,
        'expense_date' => Carbon::now()->format('Y-m-d'),
    ]);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('dashboard')
        ->has('summary.by_category', 1)
        ->where('summary.by_category.0.previous_total', fn ($value) => (float) $value === 75.0)
    );
});

test('recent expenses returns up to 10 items', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $category = Category::factory()->create();
    Expense::factory()->count(15)->create([
        'user_id' => $user->id,
        'household_id' => $user->household_id,
        'category_id' => $category->id,
        'expense_date' => Carbon::now()->format('Y-m-d'),
    ]);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('dashboard')
        ->has('summary.recent_expenses', 10)
    );
});
