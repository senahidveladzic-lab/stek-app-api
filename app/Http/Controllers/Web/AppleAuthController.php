<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class AppleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('apple')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $appleUser = Socialite::driver('apple')->user();

        $user = DB::transaction(function () use ($appleUser) {
            $existingUser = User::query()
                ->where('apple_id', $appleUser->getId())
                ->orWhere('email', $appleUser->getEmail())
                ->first();

            if ($existingUser) {
                if (! $existingUser->apple_id) {
                    $existingUser->update(['apple_id' => $appleUser->getId()]);
                }

                return $existingUser;
            }

            $user = User::query()->create([
                'name' => $appleUser->getName() ?? $appleUser->getEmail(),
                'email' => $appleUser->getEmail(),
                'apple_id' => $appleUser->getId(),
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

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}
