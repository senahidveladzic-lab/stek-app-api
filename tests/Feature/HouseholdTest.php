<?php

use App\Models\HouseholdInvitation;
use App\Models\User;

it('creates a household on web registration', function () {
    $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::query()->where('email', 'john@example.com')->first();
    expect($user->household_id)->not->toBeNull();

    $household = $user->household;
    expect($household->owner_id)->toBe($user->id)
        ->and($household->name)->toBe('John Doe')
        ->and($household->default_currency)->toBe('BAM');
});

it('creates a household on api registration', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertCreated();

    $user = User::query()->where('email', 'jane@example.com')->first();
    expect($user->household_id)->not->toBeNull()
        ->and($user->household->owner_id)->toBe($user->id);
});

it('shows household settings page', function () {
    $user = User::factory()->withHousehold()->create();

    $this->actingAs($user)
        ->get('/household')
        ->assertSuccessful();
});

it('allows owner to update household', function () {
    $user = User::factory()->withHousehold()->create();

    $this->actingAs($user)
        ->patch('/household', [
            'name' => 'Updated Name',
            'default_currency' => 'EUR',
        ])
        ->assertRedirect();

    $user->household->refresh();
    expect($user->household->name)->toBe('Updated Name')
        ->and($user->household->default_currency)->toBe('EUR');
});

it('prevents non-owner from updating household', function () {
    $owner = User::factory()->withHousehold()->create();
    $member = User::factory()->create(['household_id' => $owner->household_id]);

    $this->actingAs($member)
        ->patch('/household', ['name' => 'Hacked'])
        ->assertForbidden();
});

it('allows owner to invite a member', function () {
    $user = User::factory()->withHousehold()->create();

    $this->actingAs($user)
        ->post('/household/invite', ['email' => 'newmember@example.com'])
        ->assertRedirect();

    $this->assertDatabaseHas('household_invitations', [
        'household_id' => $user->household_id,
        'email' => 'newmember@example.com',
    ]);
});

it('prevents duplicate pending invitations', function () {
    $user = User::factory()->withHousehold()->create();

    HouseholdInvitation::factory()->create([
        'household_id' => $user->household_id,
        'email' => 'existing@example.com',
    ]);

    $this->actingAs($user)
        ->post('/household/invite', ['email' => 'existing@example.com'])
        ->assertSessionHasErrors('email');
});

it('allows accepting an invitation', function () {
    $owner = User::factory()->withHousehold()->create();
    $invitation = HouseholdInvitation::factory()->create([
        'household_id' => $owner->household_id,
        'email' => 'invited@example.com',
    ]);

    $invitedUser = User::factory()->withHousehold()->create(['email' => 'invited@example.com']);

    $this->actingAs($invitedUser)
        ->get("/household/invite/{$invitation->token}")
        ->assertRedirect(route('household.show'));

    $invitedUser->refresh();
    expect($invitedUser->household_id)->toBe($owner->household_id);
    expect($invitation->fresh()->accepted_at)->not->toBeNull();
});

it('allows owner to remove a member', function () {
    $owner = User::factory()->withHousehold()->create();
    $member = User::factory()->create(['household_id' => $owner->household_id]);

    $this->actingAs($owner)
        ->delete("/household/members/{$member->id}")
        ->assertRedirect();

    $member->refresh();
    expect($member->household_id)->toBeNull();
});

it('prevents owner from removing themselves', function () {
    $owner = User::factory()->withHousehold()->create();

    $this->actingAs($owner)
        ->delete("/household/members/{$owner->id}")
        ->assertForbidden();
});

it('prevents non-owner from removing members', function () {
    $owner = User::factory()->withHousehold()->create();
    $member = User::factory()->create(['household_id' => $owner->household_id]);
    $anotherMember = User::factory()->create(['household_id' => $owner->household_id]);

    $this->actingAs($member)
        ->delete("/household/members/{$anotherMember->id}")
        ->assertForbidden();
});

it('enforces member limit on invitations', function () {
    $user = User::factory()->withHousehold()->create();
    $household = $user->household;
    $household->update(['max_members' => 2]);

    User::factory()->create(['household_id' => $household->id]);

    $this->actingAs($user)
        ->post('/household/invite', ['email' => 'overflow@example.com'])
        ->assertForbidden();
});
