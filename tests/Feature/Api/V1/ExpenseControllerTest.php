<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->withHousehold()->create();
    $this->category = Category::factory()->create(['name' => 'restaurant']);
});

it('returns cursor paginated expenses without date filter', function () {
    Expense::factory()->count(5)->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
    ]);

    Sanctum::actingAs($this->user);

    $response = $this->getJson('/api/v1/expenses')
        ->assertSuccessful()
        ->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['path', 'per_page', 'next_cursor', 'prev_cursor'],
        ]);

    expect($response->json('data'))->toHaveCount(5);
});

it('does not expose total count in response', function () {
    Sanctum::actingAs($this->user);

    $response = $this->getJson('/api/v1/expenses')->assertSuccessful();

    expect($response->json('meta'))->not->toHaveKey('total');
});

it('returns only household expenses', function () {
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

    Sanctum::actingAs($this->user);

    $response = $this->getJson('/api/v1/expenses')->assertSuccessful();

    expect($response->json('data'))->toHaveCount(3);
});

it('can filter by category', function () {
    $other = Category::factory()->create(['name' => 'transport']);

    Expense::factory()->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
    ]);
    Expense::factory()->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $other->id,
    ]);

    Sanctum::actingAs($this->user);

    $response = $this->getJson("/api/v1/expenses?category_id={$this->category->id}")
        ->assertSuccessful();

    expect($response->json('data'))->toHaveCount(1);
});

it('can filter by date range', function () {
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

    Sanctum::actingAs($this->user);

    $response = $this->getJson('/api/v1/expenses?from=2026-01-01&to=2026-01-31')
        ->assertSuccessful();

    expect($response->json('data'))->toHaveCount(1);
});

it('supports cursor based navigation', function () {
    Expense::factory()->count(25)->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
    ]);

    Sanctum::actingAs($this->user);

    $first = $this->getJson('/api/v1/expenses')->assertSuccessful();

    expect($first->json('data'))->toHaveCount(20);
    expect($first->json('meta.next_cursor'))->not->toBeNull();

    $cursor = $first->json('meta.next_cursor');
    $second = $this->getJson("/api/v1/expenses?cursor={$cursor}")->assertSuccessful();

    expect($second->json('data'))->toHaveCount(5);
    expect($second->json('meta.next_cursor'))->toBeNull();
});

it('requires authentication', function () {
    $this->getJson('/api/v1/expenses')->assertUnauthorized();
});
