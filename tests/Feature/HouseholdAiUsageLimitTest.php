<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Laravel\Paddle\Subscription;

beforeEach(function () {
    Category::factory()->create(['name' => 'cafe']);
});

function subscribeUserToPlan(User $user, string $priceId): void
{
    $subscription = $user->subscriptions()->create([
        'type' => Subscription::DEFAULT_TYPE,
        'paddle_id' => 'sub_123',
        'status' => Subscription::STATUS_ACTIVE,
    ]);

    $subscription->items()->create([
        'product_id' => 'pro_123',
        'price_id' => $priceId,
        'status' => Subscription::STATUS_ACTIVE,
        'quantity' => 1,
    ]);
}

test('starter households are blocked after reaching the monthly ai cap', function () {
    Http::fake();

    $user = User::factory()->withHousehold()->create();
    subscribeUserToPlan($user, config('billing.plans.starter.prices.monthly'));
    $limit = config('billing.plans.starter.ai_monthly_limit');

    $user->household->update([
        'ai_reports_used' => $limit,
        'ai_reports_month' => now()->startOfMonth()->toDateString(),
    ]);

    $this->actingAs($user)
        ->postJson('/api/v1/expenses/voice', ['text' => 'kafa 3 marke'])
        ->assertStatus(429)
        ->assertJsonPath('message', __('errors.ai_usage_limit_reached'));

    Http::assertNothingSent();
});

test('max households can consume the final allowed monthly ai report', function () {
    Http::fake([
        'api.openai.com/v1/chat/*' => Http::response([
            'choices' => [[
                'message' => [
                    'content' => json_encode([
                        'amount' => 3,
                        'currency' => 'BAM',
                        'category_key' => 'cafe',
                        'description' => 'Kafa',
                        'date' => '2026-03-06',
                    ]),
                ],
            ]],
        ]),
    ]);

    $user = User::factory()->withHousehold()->create();
    subscribeUserToPlan($user, config('billing.plans.max.prices.monthly'));
    $limit = config('billing.plans.max.ai_monthly_limit');

    $user->household->update([
        'ai_reports_used' => $limit - 1,
        'ai_reports_month' => now()->startOfMonth()->toDateString(),
    ]);

    $this->actingAs($user)
        ->postJson('/api/v1/expenses/voice', ['text' => 'kafa 3 marke'])
        ->assertOk()
        ->assertJsonPath('data.category_key', 'cafe');

    expect($user->household->fresh()->ai_reports_used)->toBe($limit);
});

test('monthly ai usage resets when a new month starts', function () {
    Http::fake([
        'api.openai.com/v1/chat/*' => Http::response([
            'choices' => [[
                'message' => [
                    'content' => json_encode([
                        'amount' => 3,
                        'currency' => 'BAM',
                        'category_key' => 'cafe',
                        'description' => 'Kafa',
                        'date' => '2026-03-06',
                    ]),
                ],
            ]],
        ]),
    ]);

    $user = User::factory()->withHousehold()->create();
    subscribeUserToPlan($user, config('billing.plans.starter.prices.monthly'));
    $limit = config('billing.plans.starter.ai_monthly_limit');

    $user->household->update([
        'ai_reports_used' => $limit,
        'ai_reports_month' => now()->subMonth()->startOfMonth()->toDateString(),
    ]);

    $this->actingAs($user)
        ->postJson('/api/v1/expenses/voice', ['text' => 'kafa 3 marke'])
        ->assertOk();

    $household = $user->household->fresh();

    expect($household->ai_reports_used)->toBe(1)
        ->and($household->ai_reports_month?->toDateString())->toBe(now()->startOfMonth()->toDateString());
});
