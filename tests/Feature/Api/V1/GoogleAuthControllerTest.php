<?php

use App\Models\User;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;

function makeSocialiteUser(string $id = '123456', string $email = 'john@example.com', ?string $name = 'John Doe'): SocialiteUser
{
    $socialiteUser = new SocialiteUser;
    $socialiteUser->map([
        'id' => $id,
        'email' => $email,
        'name' => $name,
    ]);

    return $socialiteUser;
}

function mockSocialite(SocialiteUser $socialiteUser): void
{
    $provider = Mockery::mock(GoogleProvider::class);
    $provider->shouldReceive('stateless')->andReturnSelf();
    $provider->shouldReceive('userFromToken')->andReturn($socialiteUser);

    $socialite = Mockery::mock(SocialiteFactory::class);
    $socialite->shouldReceive('driver')->with('google')->andReturn($provider);

    app()->instance(SocialiteFactory::class, $socialite);
}

it('creates a new account when signing in with Google for the first time', function () {
    mockSocialite(makeSocialiteUser());

    $this->postJson('/api/v1/auth/google', ['id_token' => 'valid-token'])
        ->assertOk()
        ->assertJsonStructure(['token', 'user'])
        ->assertJsonPath('user.email', 'john@example.com')
        ->assertJsonPath('user.subscription_active', false);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'google_id' => '123456',
    ]);
});

it('returns an existing user matched by google_id', function () {
    $user = User::factory()->withHousehold()->create(['google_id' => '123456']);

    mockSocialite(makeSocialiteUser(id: '123456', email: $user->email));

    $response = $this->postJson('/api/v1/auth/google', ['id_token' => 'valid-token']);

    $response->assertSuccessful()
        ->assertJsonPath('user.id', $user->id)
        ->assertJsonStructure(['user' => ['subscription_active']]);

    $this->assertDatabaseCount('users', 1);
});

it('links google_id to existing email/password user on first Google sign-in', function () {
    $user = User::factory()->withHousehold()->create([
        'email' => 'john@example.com',
        'google_id' => null,
    ]);

    mockSocialite(makeSocialiteUser(id: '999', email: 'john@example.com'));

    $this->postJson('/api/v1/auth/google', ['id_token' => 'valid-token'])
        ->assertSuccessful();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'google_id' => '999',
    ]);
    $this->assertDatabaseCount('users', 1);
});

it('returns 422 when id_token is missing', function () {
    $this->postJson('/api/v1/auth/google', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['id_token']);
});

it('includes subscription_active true for a user on active trial', function () {
    $user = User::factory()->onTrial()->create(['google_id' => '123456']);

    mockSocialite(makeSocialiteUser(id: '123456', email: $user->email));

    $this->postJson('/api/v1/auth/google', ['id_token' => 'valid-token'])
        ->assertOk()
        ->assertJsonPath('user.subscription_active', true);
});
