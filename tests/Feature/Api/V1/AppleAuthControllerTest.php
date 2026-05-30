<?php

use App\Models\User;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Two\User as SocialiteUser;
use SocialiteProviders\Apple\Provider as AppleProvider;

function makeAppleSocialiteUser(string $id = 'apple.123456', ?string $email = 'john@example.com', ?string $name = 'John Doe'): SocialiteUser
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

it('creates a new account when signing in with Apple for the first time', function () {
    mockAppleSocialite(makeAppleSocialiteUser());

    $this->postJson('/api/v1/auth/apple', ['identity_token' => 'valid-token'])
        ->assertOk()
        ->assertJsonStructure(['token', 'user'])
        ->assertJsonPath('user.email', 'john@example.com')
        ->assertJsonPath('user.subscription_active', true);

    $user = \App\Models\User::where('email', 'john@example.com')->first();

    $this->assertNotNull($user->trial_ends_at);
    $this->assertNotNull($user->household_id);
    $this->assertDatabaseHas('households', ['owner_id' => $user->id]);
});

it('returns an existing user matched by apple_id', function () {
    $user = User::factory()->withHousehold()->create(['apple_id' => 'apple.123456']);

    mockAppleSocialite(makeAppleSocialiteUser(id: 'apple.123456', email: $user->email));

    $this->postJson('/api/v1/auth/apple', ['identity_token' => 'valid-token'])
        ->assertSuccessful()
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonStructure(['user' => ['subscription_active']]);

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

it('matches returning Apple user by apple_id when email is null', function () {
    $user = User::factory()->withHousehold()->create(['apple_id' => 'apple.123456']);

    mockAppleSocialite(makeAppleSocialiteUser(id: 'apple.123456', email: null));

    $this->postJson('/api/v1/auth/apple', ['identity_token' => 'valid-token'])
        ->assertOk()
        ->assertJsonPath('user.id', $user->id);

    $this->assertDatabaseCount('users', 1);
});

it('returns 422 when Apple provides no email and no existing account is found', function () {
    mockAppleSocialite(makeAppleSocialiteUser(id: 'apple.new', email: null));

    $this->postJson('/api/v1/auth/apple', ['identity_token' => 'valid-token'])
        ->assertUnprocessable();

    $this->assertDatabaseMissing('users', ['apple_id' => 'apple.new']);
});

it('returns 422 when identity_token is missing', function () {
    $this->postJson('/api/v1/auth/apple', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['identity_token']);
});

it('includes subscription_active true for a user on active trial', function () {
    $user = User::factory()->onTrial()->create(['apple_id' => 'apple.123456']);

    mockAppleSocialite(makeAppleSocialiteUser(id: 'apple.123456', email: $user->email));

    $this->postJson('/api/v1/auth/apple', ['identity_token' => 'valid-token'])
        ->assertOk()
        ->assertJsonPath('user.subscription_active', true);
});
