<?php

use App\Models\User;
use Laravel\Paddle\Subscription;

test('billing page is displayed for authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('billing.show'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('settings/billing'));
});

test('unsubscribed users with no trial are redirected to billing from the dashboard', function () {
    $user = User::factory()->withHousehold()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('billing.show'));
});

test('users on active trial can use the app', function () {
    $user = User::factory()->withHousehold()->onTrial()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

test('users with expired trial are redirected to billing', function () {
    $user = User::factory()->withHousehold()->withExpiredTrial()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('billing.show'));
});

test('users on active trial receive trial info on billing page', function () {
    $user = User::factory()->onTrial()->create();

    $this->actingAs($user)
        ->get(route('billing.show'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('settings/billing')
            ->where('billing.on_active_trial', true)
            ->whereNotNull('billing.trial_ends_at')
        );
});

test('users with internal access can use subscribed routes', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

test('unsubscribed api requests receive a payment required response', function () {
    $user = User::factory()->withHousehold()->create();

    $this->actingAs($user)
        ->getJson('/api/v1/expenses')
        ->assertStatus(402);
});

test('billing page includes ai_usage for regular users', function () {
    $user = User::factory()->withHousehold()->create();

    $this->actingAs($user)
        ->get(route('billing.show'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('settings/billing')
            ->has('ai_usage')
            ->where('ai_usage.used', 0)
            ->where('ai_usage.remaining', fn ($remaining) => $remaining > 0)
            ->whereType('ai_usage.total', 'integer')
            ->whereType('ai_usage.reset_date', 'string')
        );
});

test('billing page has null ai_usage for users with internal access', function () {
    $user = User::factory()->withInternalAccess()->create();

    $this->actingAs($user)
        ->get(route('billing.show'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('settings/billing')
            ->where('ai_usage', null)
        );
});

test('subscribed users can use subscribed routes', function () {
    $user = User::factory()->withHousehold()->create();
    $subscription = $user->subscriptions()->create([
        'type' => Subscription::DEFAULT_TYPE,
        'paddle_id' => 'sub_123',
        'status' => Subscription::STATUS_ACTIVE,
    ]);

    $subscription->items()->create([
        'product_id' => 'pro_123',
        'price_id' => config('billing.plans.starter.prices.monthly'),
        'status' => Subscription::STATUS_ACTIVE,
        'quantity' => 1,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});
