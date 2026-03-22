<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        app()->setLocale($locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $available = config('locale.available', ['bs', 'en']);

        if ($request->user() && in_array($request->user()->locale, $available)) {
            return $request->user()->locale;
        }

        $sessionLocale = session('locale');
        if ($sessionLocale && in_array($sessionLocale, $available)) {
            return $sessionLocale;
        }

        return config('locale.default', 'bs');
    }
}
