<?php

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;

test('guests cannot access budgets', function () {
    $this->get(route('budgets.index'))->assertRedirect(route('login'));
});

test('authenticated users can view the budgets page', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $response = $this->get(route('budgets.index'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('budgets/index'));
});

test('users can save an overall budget', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $month = Carbon::now()->startOfMonth()->format('Y-m-d');

    $this->post(route('budgets.store'), [
        'month' => $month,
        'overall_amount' => 2000,
        'categories' => [],
    ])->assertRedirect();

    $budget = Budget::query()
        ->where('household_id', $user->household_id)
        ->whereNull('category_id')
        ->first();

    expect($budget)->not->toBeNull();
    expect((float) $budget->amount)->toBe(2000.0);
});

test('users can save category budgets', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $category = Category::factory()->create();
    $month = Carbon::now()->startOfMonth()->format('Y-m-d');

    $this->post(route('budgets.store'), [
        'month' => $month,
        'overall_amount' => 2000,
        'categories' => [
            ['category_id' => $category->id, 'amount' => 500],
        ],
    ])->assertRedirect();

    $budget = Budget::query()
        ->where('household_id', $user->household_id)
        ->where('category_id', $category->id)
        ->first();

    expect($budget)->not->toBeNull();
    expect((float) $budget->amount)->toBe(500.0);
});

test('saving budgets updates existing entries via upsert', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $month = Carbon::now()->startOfMonth()->format('Y-m-d');

    Budget::factory()->create([
        'household_id' => $user->household_id,
        'category_id' => null,
        'month' => $month,
        'amount' => 1500,
    ]);

    $this->post(route('budgets.store'), [
        'month' => $month,
        'overall_amount' => 2500,
        'categories' => [],
    ])->assertRedirect();

    $budgets = Budget::query()
        ->where('household_id', $user->household_id)
        ->whereNull('category_id')
        ->get();

    expect($budgets)->toHaveCount(1);
    expect((float) $budgets->first()->amount)->toBe(2500.0);
});

test('removing a category budget deletes it', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $category = Category::factory()->create();
    $month = Carbon::now()->startOfMonth()->format('Y-m-d');

    Budget::factory()->create([
        'household_id' => $user->household_id,
        'category_id' => $category->id,
        'month' => $month,
        'amount' => 500,
    ]);

    $this->post(route('budgets.store'), [
        'month' => $month,
        'overall_amount' => 2000,
        'categories' => [],
    ])->assertRedirect();

    $budget = Budget::query()
        ->where('household_id', $user->household_id)
        ->where('category_id', $category->id)
        ->first();

    expect($budget)->toBeNull();
});

test('budgets are auto-copied from previous month when none exist', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $prevMonth = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
    $currentMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

    $category = Category::factory()->create();

    Budget::factory()->create([
        'household_id' => $user->household_id,
        'category_id' => null,
        'month' => $prevMonth,
        'amount' => 1800,
    ]);

    Budget::factory()->create([
        'household_id' => $user->household_id,
        'category_id' => $category->id,
        'month' => $prevMonth,
        'amount' => 400,
    ]);

    $response = $this->get(route('budgets.index', ['month' => $currentMonth]));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('overall_budget', fn ($value) => (float) $value === 1800.0)
        ->has('category_budgets', 1)
        ->where('category_budgets.0.amount', fn ($value) => (float) $value === 400.0)
    );

    $copiedOverall = Budget::query()
        ->where('household_id', $user->household_id)
        ->whereNull('category_id')
        ->whereDate('month', $currentMonth)
        ->first();

    expect($copiedOverall)->not->toBeNull();
    expect((float) $copiedOverall->amount)->toBe(1800.0);
});

test('auto-copy does not happen when current month already has budgets', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $prevMonth = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
    $currentMonth = Carbon::now()->startOfMonth()->format('Y-m-d');

    Budget::factory()->create([
        'household_id' => $user->household_id,
        'category_id' => null,
        'month' => $prevMonth,
        'amount' => 3000,
    ]);

    Budget::factory()->create([
        'household_id' => $user->household_id,
        'category_id' => null,
        'month' => $currentMonth,
        'amount' => 2000,
    ]);

    $response = $this->get(route('budgets.index', ['month' => $currentMonth]));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('overall_budget', fn ($value) => (float) $value === 2000.0)
    );
});

test('dashboard includes budget data', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $month = Carbon::now()->startOfMonth()->format('Y-m-d');

    Budget::factory()->create([
        'household_id' => $user->household_id,
        'category_id' => null,
        'month' => $month,
        'amount' => 2000,
    ]);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('dashboard')
        ->where('summary.budget', fn ($value) => (float) $value === 2000.0)
    );
});

test('dashboard returns null budget when none set', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('dashboard')
        ->where('summary.budget', null)
    );
});

test('dashboard by_category includes budget per category', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->actingAs($user);

    $category = Category::factory()->create();
    $month = Carbon::now()->startOfMonth()->format('Y-m-d');

    \App\Models\Expense::factory()->create([
        'user_id' => $user->id,
        'household_id' => $user->household_id,
        'category_id' => $category->id,
        'amount' => 300,
        'expense_date' => Carbon::now()->format('Y-m-d'),
    ]);

    Budget::factory()->create([
        'household_id' => $user->household_id,
        'category_id' => $category->id,
        'month' => $month,
        'amount' => 500,
    ]);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('dashboard')
        ->has('summary.by_category', 1)
        ->where('summary.by_category.0.budget', fn ($value) => (float) $value === 500.0)
    );
});
