<?php

use App\Models\Household;
use App\Models\User;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;

function makeSocialiteUser(string $id = '123456', string $email = 'john@example.com', string $name = 'John Doe'): SocialiteUser
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

it('creates a new user and household when signing in with Google for the first time', function () {
    mockSocialite(makeSocialiteUser());

    $response = $this->postJson('/api/v1/auth/google', ['id_token' => 'valid-token']);

    $response->assertSuccessful()
        ->assertJsonStructure(['token', 'user']);

    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
        'google_id' => '123456',
    ]);

    $user = User::query()->where('email', 'john@example.com')->first();
    $this->assertNotNull($user->household_id);
    $this->assertDatabaseHas('households', ['owner_id' => $user->id]);
});

it('returns an existing user matched by google_id', function () {
    $user = User::factory()->withHousehold()->create(['google_id' => '123456']);

    mockSocialite(makeSocialiteUser(id: '123456', email: $user->email));

    $response = $this->postJson('/api/v1/auth/google', ['id_token' => 'valid-token']);

    $response->assertSuccessful()
        ->assertJsonPath('user.id', $user->id);

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
