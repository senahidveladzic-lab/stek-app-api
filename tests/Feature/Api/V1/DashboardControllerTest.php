<?php

use App\Models\Household;
use App\Models\User;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;

it('requires authentication', function () {
    $this->getJson('/api/v1/dashboard/summary')->assertUnauthorized();
});

it('returns ai_usage for a regular subscribed user', function () {
    $user = User::factory()->withHousehold()->onTrial()->create();

    $household = Household::query()->find($user->household_id);
    $currentMonth = Carbon::now()->startOfMonth()->toDateString();

    $household->forceFill([
        'ai_reports_used' => 10,
        'ai_reports_month' => $currentMonth,
    ])->save();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/dashboard/summary')->assertSuccessful();

    $aiUsage = $response->json('data.ai_usage');

    expect($aiUsage)->not->toBeNull()
        ->and($aiUsage['used'])->toBe(10)
        ->and($aiUsage['total'])->toBeInt()
        ->and($aiUsage['remaining'])->toBe($aiUsage['total'] - 10)
        ->and($aiUsage['reset_date'])->toBe(Carbon::now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'));
});

it('returns ai_usage used as 0 when ai_reports_month is a previous month', function () {
    $user = User::factory()->withHousehold()->onTrial()->create();

    $household = Household::query()->find($user->household_id);

    $household->forceFill([
        'ai_reports_used' => 50,
        'ai_reports_month' => Carbon::now()->subMonth()->startOfMonth()->toDateString(),
    ])->save();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/dashboard/summary')->assertSuccessful();

    $aiUsage = $response->json('data.ai_usage');

    expect($aiUsage['used'])->toBe(0)
        ->and($aiUsage['remaining'])->toBe($aiUsage['total']);
});

it('returns null ai_usage for users with internal access', function () {
    $user = User::factory()->withHousehold()->withInternalAccess()->create();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/dashboard/summary')->assertSuccessful();

    expect($response->json('data.ai_usage'))->toBeNull();
});

it('remaining is never negative', function () {
    $user = User::factory()->withHousehold()->onTrial()->create();

    $household = Household::query()->find($user->household_id);
    $currentMonth = Carbon::now()->startOfMonth()->toDateString();

    $household->forceFill([
        'ai_reports_used' => 9999,
        'ai_reports_month' => $currentMonth,
    ])->save();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/dashboard/summary')->assertSuccessful();

    expect($response->json('data.ai_usage.remaining'))->toBeGreaterThanOrEqual(0);
});
