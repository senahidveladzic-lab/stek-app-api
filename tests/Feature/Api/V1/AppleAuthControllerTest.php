<?php

use App\Models\User;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Two\User as SocialiteUser;
use SocialiteProviders\Apple\Provider as AppleProvider;

function makeAppleSocialiteUser(string $id = 'apple.123456', string $email = 'john@example.com', string $name = 'John Doe'): SocialiteUser
{
    $socialiteUser = new SocialiteUser;
    $socialiteUser->map([
        'id' => $id,
        'email' => $email,
        'name' => $name,
    ]);

    return $socialiteUser;
}

function mockAppleSocialite(SocialiteUser $socialiteUser): void
{
    $provider = Mockery::mock(AppleProvider::class);
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('userFromToken')->andReturn($socialiteUser);

    $socialite = Mockery::mock(SocialiteFactory::class);
    $socialite->shouldReceive('driver')->with('apple')->andReturn($provider);

    app()->instance(SocialiteFactory::class, $socialite);
}

it('returns 403 when signing in with Apple for an unregistered account', function () {
    mockAppleSocialite(makeAppleSocialiteUser());

    $this->postJson('/api/v1/auth/apple', ['identity_token' => 'valid-token'])
        ->assertForbidden();

    $this->assertDatabaseMissing('users', ['email' => 'john@example.com']);
});

it('returns an existing user matched by apple_id', function () {
    $user = User::factory()->withHousehold()->create(['apple_id' => 'apple.123456']);

    mockAppleSocialite(makeAppleSocialiteUser(id: 'apple.123456', email: $user->email));

    $this->postJson('/api/v1/auth/apple', ['identity_token' => 'valid-token'])
        ->assertSuccessful()
        ->assertJsonPath('user.id', $user->id);

    $this->assertDatabaseCount('users', 1);
});

it('links apple_id to existing email/password user on first Apple sign-in', function () {
    $user = User::factory()->withHousehold()->create([
        'email' => 'john@example.com',
        'apple_id' => null,
    ]);

    mockAppleSocialite(makeAppleSocialiteUser(id: 'apple.999', email: 'john@example.com'));

    $this->postJson('/api/v1/auth/apple', ['identity_token' => 'valid-token'])
        ->assertSuccessful();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'apple_id' => 'apple.999',
    ]);
    $this->assertDatabaseCount('users', 1);
});

it('returns 422 when identity_token is missing', function () {
    $this->postJson('/api/v1/auth/apple', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['identity_token']);
});
