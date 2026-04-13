<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->category = Category::factory()->create(['name' => 'restaurant']);
});

it('can list expenses for authenticated user', function () {
    Expense::factory()->count(3)->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
    ]);

    $otherUser = User::factory()->withHousehold()->create();
    Expense::factory()->count(2)->create([
        'user_id' => $otherUser->id,
        'household_id' => $otherUser->household_id,
        'category_id' => $this->category->id,
    ]);

    $this->actingAs($this->user)
        ->get('/expenses')
        ->assertSuccessful();
});

it('can store an expense', function () {
    $this->actingAs($this->user)
        ->post('/expenses', [
            'amount' => 25.50,
            'category_id' => $this->category->id,
            'description' => 'Groceries',
            'expense_date' => '2026-03-06',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('expenses', [
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'amount' => 25.50,
    ]);
});

it('validates required fields on store', function () {
    $this->actingAs($this->user)
        ->post('/expenses', [])
        ->assertSessionHasErrors(['amount', 'category_id', 'expense_date']);
});

it('can update an expense', function () {
    $expense = Expense::factory()->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
    ]);

    $this->actingAs($this->user)
        ->put("/expenses/{$expense->id}", [
            'amount' => 99.99,
        ])
        ->assertRedirect();

    $expense->refresh();
    expect($expense->amount)->toBe('99.99');
});

it('can delete an expense', function () {
    $expense = Expense::factory()->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
    ]);

    $this->actingAs($this->user)
        ->delete("/expenses/{$expense->id}")
        ->assertRedirect();

    $this->assertSoftDeleted('expenses', ['id' => $expense->id]);
});

it('cannot update another households expense', function () {
    $otherUser = User::factory()->withHousehold()->create();
    $expense = Expense::factory()->create([
        'user_id' => $otherUser->id,
        'household_id' => $otherUser->household_id,
        'category_id' => $this->category->id,
    ]);

    $this->actingAs($this->user)
        ->put("/expenses/{$expense->id}", ['amount' => 1.00])
        ->assertForbidden();
});

it('cannot delete another households expense', function () {
    $otherUser = User::factory()->withHousehold()->create();
    $expense = Expense::factory()->create([
        'user_id' => $otherUser->id,
        'household_id' => $otherUser->household_id,
        'category_id' => $this->category->id,
    ]);

    $this->actingAs($this->user)
        ->delete("/expenses/{$expense->id}")
        ->assertForbidden();
});

it('allows household members to update each others expenses', function () {
    $member = User::factory()->create(['household_id' => $this->user->household_id]);
    $expense = Expense::factory()->create([
        'user_id' => $member->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
    ]);

    $this->actingAs($this->user)
        ->put("/expenses/{$expense->id}", ['amount' => 50.00])
        ->assertRedirect();
});

it('requires authentication to access expenses', function () {
    $this->get('/expenses')->assertRedirect('/login');
    $this->post('/expenses', [])->assertRedirect('/login');
});

it('can filter expenses by category', function () {
    $otherCategory = Category::factory()->create(['name' => 'transport']);
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
    ]);
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $otherCategory->id,
    ]);

    $this->actingAs($this->user)
        ->get("/expenses?category_id={$this->category->id}")
        ->assertSuccessful();
});

it('can filter expenses by date range', function () {
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
        'expense_date' => '2026-01-15',
    ]);
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
        'expense_date' => '2026-03-01',
    ]);

    $this->actingAs($this->user)
        ->get('/expenses?from=2026-01-01&to=2026-01-31')
        ->assertSuccessful();
});

it('stores converted amount when currency differs from household default', function () {
    $this->actingAs($this->user)
        ->post('/expenses', [
            'amount' => 15.00,
            'currency' => 'EUR',
            'category_id' => $this->category->id,
            'expense_date' => '2026-03-06',
        ])
        ->assertRedirect();

    $expense = Expense::query()->where('user_id', $this->user->id)->latest()->first();
    expect($expense->original_amount)->toBe('15.00')
        ->and($expense->original_currency)->toBe('EUR')
        ->and($expense->currency)->toBe('BAM');
});

it('includes month and year totals in index', function () {
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
        'amount' => 100,
        'expense_date' => now()->format('Y-m-d'),
    ]);

    $response = $this->actingAs($this->user)
        ->get('/expenses')
        ->assertSuccessful();

    $props = $response->viewData('page')['props'];
    expect($props['month_total'])->toBe(100.0)
        ->and($props['year_total'])->toBe(100.0);
});
