<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GoogleLoginRequest;
use App\Models\Household;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function __invoke(GoogleLoginRequest $request): JsonResponse
    {
        $googleUser = Socialite::driver('google')
            ->stateless()
            ->userFromToken($request->string('id_token'));

        $user = DB::transaction(function () use ($googleUser) {
            $existingUser = User::query()
                ->where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($existingUser) {
                if (! $existingUser->google_id) {
                    $existingUser->update(['google_id' => $googleUser->getId()]);
                }

                return $existingUser;
            }

            $user = User::query()->create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => null,
                'trial_ends_at' => now()->addDays(14),
            ]);

            $household = Household::create([
                'name' => $user->name,
                'owner_id' => $user->id,
                'default_currency' => $user->default_currency,
            ]);

            $user->update(['household_id' => $household->id]);

            return $user;
        });

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }
}
