<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Http\Requests\UpdateLocaleRequest;
use Illuminate\Http\JsonResponse;

class UserSettingsController extends Controller
{
    public function updateLocale(UpdateLocaleRequest $request): JsonResponse
    {
        $request->user()->update(['locale' => $request->validated('locale')]);

        return response()->json(['message' => __('settings.saved')]);
    }

    public function updateCurrency(UpdateCurrencyRequest $request): JsonResponse
    {
        $request->user()->update(['default_currency' => $request->validated('currency')]);

        return response()->json(['message' => __('settings.saved')]);
    }
}
