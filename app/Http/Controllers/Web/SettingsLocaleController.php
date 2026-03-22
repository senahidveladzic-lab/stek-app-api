<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Http\Requests\UpdateLocaleRequest;
use Illuminate\Http\RedirectResponse;

class SettingsLocaleController extends Controller
{
    public function updateLocale(UpdateLocaleRequest $request): RedirectResponse
    {
        $request->user()->update(['locale' => $request->validated('locale')]);

        session(['locale' => $request->validated('locale')]);

        return back()->with('success', __('settings.saved'));
    }

    public function updateCurrency(UpdateCurrencyRequest $request): RedirectResponse
    {
        $request->user()->update(['default_currency' => $request->validated('currency')]);

        return back()->with('success', __('settings.saved'));
    }
}
