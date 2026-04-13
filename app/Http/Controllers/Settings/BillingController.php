<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\BillingPlanRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $request->user();
        $subscription = $user->subscription(config('billing.subscription_type'));
        $currentPriceId = $subscription?->items->first()?->price_id;

        return Inertia::render('settings/billing', [
            'billing' => [
                'has_internal_access' => $user->has_internal_access,
                'has_billing_access' => $user->hasBillingAccess(),
                'plans' => $this->plans(),
                'subscription' => [
                    'status' => $subscription?->status,
                    'subscribed' => $subscription?->valid() ?? false,
                    'on_grace_period' => $subscription?->onGracePeriod() ?? false,
                    'current_price_id' => $currentPriceId,
                    'ends_at' => $subscription?->ends_at?->toIso8601String(),
                    'trial_ends_at' => $subscription?->trial_ends_at?->toIso8601String(),
                ],
            ],
        ]);
    }

    public function checkout(BillingPlanRequest $request): JsonResponse
    {
        $options = $request->user()
            ->subscribe($request->validatedPriceId(), config('billing.subscription_type'))
            ->returnTo(route('billing.show'))
            ->options();

        return response()->json($options);
    }

    public function swap(BillingPlanRequest $request): RedirectResponse
    {
        $subscription = $request->user()->subscription(config('billing.subscription_type'));
        $priceId = $request->validatedPriceId();

        if (! $subscription || ! $subscription->valid()) {
            return to_route('billing.show')->with('error', __('billing.no_active_subscription'));
        }

        if ($subscription->hasPrice($priceId)) {
            return to_route('billing.show')->with('success', __('billing.plan_already_active'));
        }

        $subscription->swap($priceId);

        return to_route('billing.show')->with('success', __('billing.plan_updated'));
    }

    public function updatePaymentMethod(Request $request): RedirectResponse
    {
        $subscription = $request->user()->subscription(config('billing.subscription_type'));

        abort_if(! $subscription || ! $subscription->valid(), 404);

        return $subscription->redirectToUpdatePaymentMethod();
    }

    public function cancel(Request $request): RedirectResponse
    {
        $subscription = $request->user()->subscription(config('billing.subscription_type'));

        if (! $subscription || ! $subscription->valid()) {
            return to_route('billing.show')->with('error', __('billing.no_active_subscription'));
        }

        $subscription->cancel();

        return to_route('billing.show')->with('success', __('billing.subscription_canceled'));
    }

    public function resume(Request $request): RedirectResponse
    {
        $subscription = $request->user()->subscription(config('billing.subscription_type'));

        if (! $subscription || ! $subscription->onGracePeriod()) {
            return to_route('billing.show')->with('error', __('billing.no_resumable_subscription'));
        }

        $subscription->resume();

        return to_route('billing.show')->with('success', __('billing.subscription_resumed'));
    }

    /**
     * @return array<string, array{name: string, description: string, prices: array<string, string>}>
     */
    protected function plans(): array
    {
        /** @var array<string, array{name: string, description: string, prices: array<string, string>}> $plans */
        $plans = config('billing.plans', []);

        return $plans;
    }
}
