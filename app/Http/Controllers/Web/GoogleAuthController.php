<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Household;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

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

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}
