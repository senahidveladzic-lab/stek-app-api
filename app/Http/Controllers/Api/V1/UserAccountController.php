<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAccountController extends Controller
{
    /**
     * Delete the authenticated user's account and revoke all their tokens.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->subscriptions()->active()->each(fn ($subscription) => $subscription->cancelNow());

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => __('settings.delete_account_success')]);
    }
}
