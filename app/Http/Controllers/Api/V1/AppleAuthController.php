<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AppleLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class AppleAuthController extends Controller
{
    public function __invoke(AppleLoginRequest $request): JsonResponse
    {
        $appleUser = Socialite::driver('apple')
            ->stateless()
            ->userFromToken($request->string('identity_token'));

        $user = User::query()
            ->where('apple_id', $appleUser->getId())
            ->orWhere('email', $appleUser->getEmail())
            ->first();

        if (! $user) {
            return response()->json([
                'message' => __('auth.registration_web_only'),
            ], 403);
        }

        if (! $user->apple_id) {
            $user->update(['apple_id' => $appleUser->getId()]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }
}
