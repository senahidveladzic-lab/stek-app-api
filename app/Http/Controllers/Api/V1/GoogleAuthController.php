<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GoogleLoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function __invoke(GoogleLoginRequest $request): JsonResponse
    {
        $googleUser = Socialite::driver('google')
            ->stateless()
            ->userFromToken($request->string('id_token'));

        $user = User::query()
            ->where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (! $user) {
            return response()->json([
                'message' => __('auth.registration_web_only'),
            ], 403);
        }

        if (! $user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }
}
