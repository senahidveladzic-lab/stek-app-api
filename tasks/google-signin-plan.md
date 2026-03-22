# Google Sign-In Plan

Both sides of the integration — the Laravel API and the Expo app.

---

## Overview of the Flow

```
Expo app
  → GoogleSignin.signIn()         (native Google SDK)
  → receives idToken (JWT)
  → POST /v1/auth/google { id_token }
Laravel API
  → Socialite::driver('google')->userFromToken($idToken)
  → finds or creates User + Household
  → returns { token, user } (Sanctum token)
Expo app
  → stores token in SecureStore (same as email/password login)
```

The mobile app **never** does a browser redirect. Google's native SDK handles the OAuth popup and returns an `idToken` directly. The backend verifies it with Socialite's `userFromToken()` — no callback URL needed.

---

## Google Cloud Console Setup (do this first)

Before writing any code, create the OAuth credentials:

1. Go to [console.cloud.google.com](https://console.cloud.google.com) → APIs & Services → Credentials
2. Create **3 OAuth 2.0 Client IDs**:
   - **Web** — type: Web application. Used by Laravel Socialite on the backend. The `client_id` is your `GOOGLE_CLIENT_ID`. The `client_secret` is your `GOOGLE_CLIENT_SECRET`. No redirect URI needed (stateless verification).
   - **Android** — type: Android. Package name: `com.anonymous.troskovimobile`. Requires SHA-1 fingerprint of your keystore.
   - **iOS** — type: iOS. Bundle ID: `com.anonymous.troskovimobile`.
3. Save all three Client IDs. You will need:
   - Web Client ID → `GOOGLE_CLIENT_ID` in `.env` and `webClientId` in Expo `GoogleSignin.configure()`
   - iOS Client ID → `iosClientId` in Expo `GoogleSignin.configure()` and `iosUrlScheme` in `app.json`
   - Android Client ID → only needed in `google-services.json` (Android auto-detects it)

---

## Part 1 — Backend (Laravel API)

### Files to create / modify

| Action | File |
|--------|------|
| Install package | `composer require laravel/socialite` |
| New migration | `database/migrations/xxxx_add_google_id_to_users_table.php` |
| Modify model | `app/Models/User.php` |
| Modify config | `config/services.php` |
| New controller | `app/Http/Controllers/Api/V1/GoogleAuthController.php` |
| Modify routes | `routes/api.php` |
| New form request | `app/Http/Requests/Api/GoogleLoginRequest.php` |
| New test | `tests/Feature/Api/V1/GoogleAuthControllerTest.php` |

---

### Step 1 — Install Laravel Socialite

```bash
composer require laravel/socialite
```

Socialite ships with a Google driver built-in. No extra package needed.

---

### Step 2 — Environment variables

Add to `.env` and `.env.example`:

```env
GOOGLE_CLIENT_ID=your-web-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-web-client-secret
```

---

### Step 3 — `config/services.php`

Add to the returned array:

```php
'google' => [
    'client_id'     => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect'      => '', // not used for mobile token verification
],
```

---

### Step 4 — Migration: add `google_id` to users

```bash
php artisan make:migration add_google_id_to_users_table --table=users --no-interaction
```

Migration content:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('google_id')->nullable()->unique()->after('email');
        $table->string('password')->nullable()->change(); // allow passwordless Google users
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('google_id');
        $table->string('password')->nullable(false)->change();
    });
}
```

Run: `php artisan migrate`

---

### Step 5 — Update `User` model

Add `google_id` to `$fillable`. Make `password` nullable in the cast since Google users won't have one.

```php
protected $fillable = [
    'name',
    'email',
    'password',
    'google_id',
    'locale',
    'default_currency',
    'household_id',
];

protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'two_factor_confirmed_at' => 'datetime',
    ];
}
```

---

### Step 6 — Form Request

```bash
php artisan make:request Api/GoogleLoginRequest --no-interaction
```

```php
<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GoogleLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_token' => ['required', 'string'],
        ];
    }
}
```

---

### Step 7 — `GoogleAuthController`

```bash
php artisan make:controller Api/V1/GoogleAuthController --no-interaction
```

Full logic:

```php
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
                // Link Google ID if they previously registered with email/password
                if (! $existingUser->google_id) {
                    $existingUser->update(['google_id' => $googleUser->getId()]);
                }

                return $existingUser;
            }

            // New user — create User + Household (same as register flow)
            $user = User::query()->create([
                'name'      => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password'  => null,
            ]);

            $household = Household::create([
                'name'             => $user->name,
                'owner_id'         => $user->id,
                'default_currency' => $user->default_currency,
            ]);

            $user->update(['household_id' => $household->id]);

            return $user;
        });

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
        ]);
    }
}
```

Key notes:
- `stateless()` — skips session/CSRF, required for API use
- `userFromToken()` — verifies the `idToken` with Google's API directly (no redirect)
- Handles account linking: if a user registered with email first, links their Google ID

---

### Step 8 — Register route in `routes/api.php`

Add alongside the existing auth routes (no `auth:sanctum` middleware):

```php
use App\Http\Controllers\Api\V1\GoogleAuthController;

Route::post('auth/google', GoogleAuthController::class);
```

---

### Step 9 — Tests

```bash
php artisan make:test Api/V1/GoogleAuthControllerTest --pest --no-interaction
```

Test cases to cover:
- New user via Google creates User + Household and returns token
- Existing user by `google_id` returns existing user's token
- Existing user by `email` (previously email/password) gets `google_id` linked
- Invalid/expired `id_token` returns 422 or 500 (Socialite throws)
- Missing `id_token` returns 422 validation error

Use `Socialite::shouldReceive('driver')->...` to mock Socialite in tests so no real Google API calls are made.

---

### Step 10 — Run Pint

```bash
vendor/bin/pint --dirty --format agent
```

---

## Part 2 — Frontend (Expo)

### Files to create / modify

| Action | File |
|--------|------|
| Install package | `@react-native-google-signin/google-signin` |
| Modify config | `app.json` |
| New API function | `src/api/auth.ts` (add `googleLogin`) |
| Modify store | `src/stores/authStore.ts` (add `loginWithGoogle`) |
| Modify UI | Login screen (add Google button) |

---

### Important: Expo Go won't work

`@react-native-google-signin/google-signin` requires native code. It **does not work with Expo Go**. You must use one of:
- **EAS Build** (recommended for this project — just run `eas build`)
- **Expo Dev Client** (`npx expo run:ios` / `npx expo run:android`)

The project already has `newArchEnabled: true` — the library supports New Architecture.

---

### Step 1 — Install the package

```bash
npx expo install @react-native-google-signin/google-signin
```

---

### Step 2 — Update `app.json`

Add the plugin. Since the project does **not** use Firebase, provide the `iosUrlScheme` (reverse of the iOS Client ID):

```json
{
  "expo": {
    "plugins": [
      "expo-router",
      [
        "@react-native-google-signin/google-signin",
        {
          "iosUrlScheme": "com.googleusercontent.apps.YOUR_IOS_CLIENT_ID_HERE"
        }
      ],
      ...existing plugins
    ]
  }
}
```

The `iosUrlScheme` is the iOS Client ID reversed. Example: if iOS Client ID is `123456-abc.apps.googleusercontent.com`, the scheme is `com.googleusercontent.apps.123456-abc`.

---

### Step 3 — Add `googleLogin` to `src/api/auth.ts`

Add alongside existing auth API calls:

```ts
googleLogin: (idToken: string) =>
  client.post('/v1/auth/google', { id_token: idToken }),
```

---

### Step 4 — Update `src/stores/authStore.ts`

Add `loginWithGoogle` to the interface and implementation:

```ts
import {
  GoogleSignin,
  isSuccessResponse,
  statusCodes,
} from '@react-native-google-signin/google-signin';

// In the interface:
loginWithGoogle: () => Promise<void>;

// Configure once — call this at app startup (e.g. in _layout.tsx or the store itself)
GoogleSignin.configure({
  webClientId: 'YOUR_WEB_CLIENT_ID.apps.googleusercontent.com',
  iosClientId: 'YOUR_IOS_CLIENT_ID.apps.googleusercontent.com',
  offlineAccess: false,
});

// In the store:
loginWithGoogle: async () => {
  await GoogleSignin.hasPlayServices();
  const response = await GoogleSignin.signIn();

  if (!isSuccessResponse(response)) {
    return; // user cancelled
  }

  const { idToken } = response.data;
  if (!idToken) {
    throw new Error('No idToken received from Google');
  }

  const res = await authApi.googleLogin(idToken);
  const { token, user } = res.data;
  await SecureStore.setItemAsync('auth_token', token);
  resetLogoutGuard();
  set({ token, user });
},
```

Place `GoogleSignin.configure()` call in the app entry point (e.g. `app/_layout.tsx`) so it runs once at startup before any sign-in attempt.

---

### Step 5 — Add Google Sign-In button to the Login screen

Find the existing login screen (likely in `app/(auth)/login.tsx` or similar).

Add a "Continue with Google" button that calls `loginWithGoogle()` from the auth store:

```tsx
import { useAuthStore } from '@/stores/authStore';
import { statusCodes, isErrorWithCode } from '@react-native-google-signin/google-signin';

const { loginWithGoogle } = useAuthStore();

const handleGoogleSignIn = async () => {
  try {
    await loginWithGoogle();
    // navigation handled by existing auth guard/redirect logic
  } catch (error) {
    if (isErrorWithCode(error)) {
      if (error.code === statusCodes.SIGN_IN_CANCELLED) return;
      if (error.code === statusCodes.IN_PROGRESS) return;
      if (error.code === statusCodes.PLAY_SERVICES_NOT_AVAILABLE) {
        // Android only
        Alert.alert('Google Play Services not available');
        return;
      }
    }
    Alert.alert('Google sign-in failed');
  }
};
```

---

## Environment Variable Reference

| Variable | Where used | Value |
|----------|-----------|-------|
| `GOOGLE_CLIENT_ID` | Laravel `.env` | Web OAuth Client ID |
| `GOOGLE_CLIENT_SECRET` | Laravel `.env` | Web OAuth Client Secret |
| `webClientId` | Expo `GoogleSignin.configure()` | Web OAuth Client ID (same as above) |
| `iosClientId` | Expo `GoogleSignin.configure()` | iOS OAuth Client ID |
| `iosUrlScheme` | `app.json` plugin config | iOS Client ID reversed |

---

## Implementation Order

1. Set up Google Cloud Console credentials (all 3 client IDs)
2. Backend: install Socialite, migration, model, controller, route, tests
3. Frontend: install package, update `app.json`, update API + store, add button
4. Build with EAS (`eas build`) — Expo Go will not work
5. Test end-to-end on a real device
