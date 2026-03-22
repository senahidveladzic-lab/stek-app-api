<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'household' => fn () => $request->user()?->household ? [
                'id' => $request->user()->household->id,
                'name' => $request->user()->household->name,
                'default_currency' => $request->user()->household->default_currency,
                'owner_id' => $request->user()->household->owner_id,
                'max_members' => $request->user()->household->max_members,
            ] : null,
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'locale' => app()->getLocale(),
            'availableLocales' => config('locale.available'),
            'translations' => fn () => json_decode(
                file_get_contents(lang_path(app()->getLocale().'.json')),
                true
            ),
            'formats' => config('locale.formats.'.app()->getLocale()),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ];
    }
}
