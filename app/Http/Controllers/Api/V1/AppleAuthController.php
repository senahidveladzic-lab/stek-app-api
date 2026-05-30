<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AppleLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\Household;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AppleAuthController extends Controller
{
    public function __invoke(AppleLoginRequest $request): JsonResponse
    {
        $appleUser = Socialite::driver('apple')
            ->stateless()
            ->userFromToken($request->string('identity_token'));

        $query = User::query()->where('apple_id', $appleUser->getId());

        if ($appleUser->getEmail()) {
            $query->orWhere('email', $appleUser->getEmail());
        }

        $user = $query->first();

        if (! $user) {
            $email = $appleUser->getEmail();

            if (! $email) {
                return response()->json([
                    'message' => 'Unable to create account: Apple did not provide an email address.',
                ], 422);
            }

            $name = $appleUser->getName() ?? Str::before($email, '@');

            $user = DB::transaction(function () use ($email, $name, $appleUser) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'apple_id' => $appleUser->getId(),
                    'password' => Str::random(32),
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
        } elseif (! $user->apple_id) {
            $user->update(['apple_id' => $appleUser->getId()]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }
}
