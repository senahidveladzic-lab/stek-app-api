<?php

use App\Models\Expense;
use App\Models\Tag;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->owner = User::factory()->withHousehold()->withInternalAccess()->create();
});

it('lists household tags', function () {
    Tag::factory()->create([
        'household_id' => $this->owner->household_id,
        'name' => 'Work',
        'color' => '#2563EB',
    ]);

    Tag::factory()->create([
        'name' => 'Other household',
        'color' => '#DC2626',
    ]);

    Sanctum::actingAs($this->owner);

    $response = $this->getJson('/api/v1/tags')->assertSuccessful();

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.name'))->toBe('Work')
        ->and($response->json('data.0.color'))->toBe('#2563EB');
});

it('creates a household tag', function () {
    Sanctum::actingAs($this->owner);

    $response = $this->postJson('/api/v1/tags', [
        'name' => 'Family',
        'color' => '#16A34A',
    ])->assertCreated();

    $response->assertJsonPath('data.name', 'Family')
        ->assertJsonPath('data.color', '#16A34A');

    $this->assertDatabaseHas('tags', [
        'household_id' => $this->owner->household_id,
        'name' => 'Family',
        'color' => '#16A34A',
    ]);
});

it('requires tag names to be unique within a household', function () {
    Tag::factory()->create([
        'household_id' => $this->owner->household_id,
        'name' => 'Family',
    ]);

    Sanctum::actingAs($this->owner);

    $this->postJson('/api/v1/tags', [
        'name' => 'Family',
        'color' => '#16A34A',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('updates a household tag', function () {
    $tag = Tag::factory()->create([
        'household_id' => $this->owner->household_id,
        'name' => 'Old',
        'color' => '#2563EB',
    ]);

    Sanctum::actingAs($this->owner);

    $this->patchJson("/api/v1/tags/{$tag->id}", [
        'name' => 'Updated',
        'color' => '#9333EA',
    ])->assertSuccessful()
        ->assertJsonPath('data.name', 'Updated')
        ->assertJsonPath('data.color', '#9333EA');
});

it('allows only the household owner to delete a tag', function () {
    $tag = Tag::factory()->create([
        'household_id' => $this->owner->household_id,
    ]);

    $member = User::factory()->withInternalAccess()->create([
        'household_id' => $this->owner->household_id,
    ]);

    Sanctum::actingAs($member);

    $this->deleteJson("/api/v1/tags/{$tag->id}")->assertForbidden();

    Sanctum::actingAs($this->owner);

    $this->deleteJson("/api/v1/tags/{$tag->id}")->assertNoContent();

    $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
});

it('detaches expenses when deleting a tag', function () {
    $tag = Tag::factory()->create([
        'household_id' => $this->owner->household_id,
    ]);

    $expense = Expense::factory()->create([
        'user_id' => $this->owner->id,
        'household_id' => $this->owner->household_id,
    ]);
    $expense->tags()->attach($tag);

    Sanctum::actingAs($this->owner);

    $this->deleteJson("/api/v1/tags/{$tag->id}")->assertNoContent();

    $this->assertDatabaseMissing('expense_tag', [
        'expense_id' => $expense->id,
        'tag_id' => $tag->id,
    ]);
});
