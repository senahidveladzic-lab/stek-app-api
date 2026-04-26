<?php

use App\Models\User;

it('allows existing users to log in via the api', function () {
    $user = User::factory()->withHousehold()->create(['password' => 'password']);

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertOk()->assertJsonStructure(['token', 'user']);
});

it('returns 422 for invalid credentials on api login', function () {
    $this->postJson('/api/v1/auth/login', [
        'email' => 'nobody@example.com',
        'password' => 'wrong',
    ])->assertUnprocessable();
});

it('blocks registration via the api', function () {
    $this->postJson('/api/v1/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
});
