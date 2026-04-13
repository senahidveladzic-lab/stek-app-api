<?php

namespace App\Services;

use App\Exceptions\AiUsageLimitExceededException;
use App\Models\Household;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class HouseholdAiUsageService
{
    public function consume(User $user): void
    {
        if ($user->has_internal_access) {
            return;
        }

        $limit = $this->monthlyLimitFor($user);

        DB::transaction(function () use ($limit, $user) {
            $household = Household::query()
                ->lockForUpdate()
                ->findOrFail($user->household_id);

            $currentMonth = now()->startOfMonth()->toDateString();

            if ($household->ai_reports_month?->toDateString() !== $currentMonth) {
                $household->forceFill([
                    'ai_reports_used' => 0,
                    'ai_reports_month' => $currentMonth,
                ])->saveQuietly();
            }

            if ($household->ai_reports_used >= $limit) {
                throw new AiUsageLimitExceededException($limit, $household->ai_reports_used);
            }

            $household->increment('ai_reports_used');
            $household->updateQuietly([
                'ai_reports_month' => $currentMonth,
            ]);
        });
    }

    public function monthlyLimitFor(User $user): int
    {
        $planKey = $this->resolvePlanKeyFor($user) ?? config('billing.default_ai_limit_plan', 'starter');

        /** @var int $limit */
        $limit = config("billing.plans.{$planKey}.ai_monthly_limit");

        return $limit;
    }

    public function resolvePlanKeyFor(User $user): ?string
    {
        $priceId = $this->activePriceIdFor($user)
            ?? $this->activePriceIdFor($user->household?->owner);

        if (! $priceId) {
            return null;
        }

        /** @var array<string, array{name: string, description: string, ai_monthly_limit: int, prices: array<string, string>}> $plans */
        $plans = config('billing.plans', []);

        foreach ($plans as $planKey => $plan) {
            if (in_array($priceId, $plan['prices'], true)) {
                return $planKey;
            }
        }

        return null;
    }

    protected function activePriceIdFor(?User $user): ?string
    {
        if (! $user) {
            return null;
        }

        $subscription = $user->subscription(config('billing.subscription_type'));

        if (! $subscription || ! $subscription->valid()) {
            return null;
        }

        /** @var string|null $priceId */
        $priceId = $subscription->items->first()?->price_id;

        return $priceId;
    }
}
