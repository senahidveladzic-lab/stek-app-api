<?php

use App\Models\User;

it('deletes the authenticated user and revokes their tokens', function () {
    $user = User::factory()->withHousehold()->create();
    $token = $user->createToken('test')->plainTextToken;

    $this->withToken($token)
        ->deleteJson('/api/v1/user')
        ->assertOk()
        ->assertJson(['message' => __('settings.delete_account_success')]);

    $this->assertModelMissing($user);
    $this->assertDatabaseEmpty('personal_access_tokens');
});

it('returns 401 when unauthenticated', function () {
    $this->deleteJson('/api/v1/user')->assertUnauthorized();
});
