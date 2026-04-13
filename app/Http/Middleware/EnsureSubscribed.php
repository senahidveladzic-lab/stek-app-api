<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->hasBillingAccess()) {
            return $next($request);
        }

        if ($request->routeIs('billing.*')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('billing.subscription_required'),
            ], 402);
        }

        /** @var RedirectResponse $response */
        $response = redirect()
            ->route('billing.show')
            ->with('error', __('billing.subscription_required'));

        return $response;
    }
}
