<?php

use App\Models\Category;
use App\Models\Expense;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->withHousehold()->withInternalAccess()->create();
    $this->category = Category::factory()->create(['name' => 'food']);
    $this->tag = Tag::factory()->create([
        'household_id' => $this->user->household_id,
        'name' => 'Family',
        'color' => '#2563EB',
    ]);
});

it('saves a tag association when creating an expense', function () {
    Sanctum::actingAs($this->user);

    $response = $this->postJson('/api/v1/expenses', [
        'amount' => 25,
        'currency' => 'BAM',
        'category_id' => $this->category->id,
        'tag_id' => $this->tag->id,
        'description' => 'Dinner',
        'expense_date' => '2026-04-26',
    ])->assertCreated();

    $response->assertJsonPath('data.tag.id', $this->tag->id)
        ->assertJsonPath('data.tag.name', 'Family');

    $this->assertDatabaseHas('expense_tag', [
        'expense_id' => $response->json('data.id'),
        'tag_id' => $this->tag->id,
    ]);
});

it('updates and clears an expense tag association', function () {
    $expense = Expense::factory()->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
    ]);

    Sanctum::actingAs($this->user);

    $this->patchJson("/api/v1/expenses/{$expense->id}", [
        'tag_id' => $this->tag->id,
    ])->assertSuccessful()
        ->assertJsonPath('data.tag.id', $this->tag->id);

    $this->patchJson("/api/v1/expenses/{$expense->id}", [
        'tag_id' => null,
    ])->assertSuccessful()
        ->assertJsonPath('data.tag', null);

    $this->assertDatabaseMissing('expense_tag', [
        'expense_id' => $expense->id,
        'tag_id' => $this->tag->id,
    ]);
});

it('filters expenses by tag id', function () {
    $matching = Expense::factory()->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
    ]);
    $matching->tags()->attach($this->tag);

    Expense::factory()->create([
        'user_id' => $this->user->id,
        'household_id' => $this->user->household_id,
        'category_id' => $this->category->id,
    ]);

    Sanctum::actingAs($this->user);

    $response = $this->getJson("/api/v1/expenses?tag_id={$this->tag->id}")
        ->assertSuccessful();

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.id'))->toBe($matching->id)
        ->and($response->json('data.0.tag.id'))->toBe($this->tag->id);
});

it('returns suggested tag id from the voice endpoint', function () {
    Http::fake([
        'api.openai.com/v1/chat/*' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'amount' => 12,
                            'currency' => 'BAM',
                            'category_key' => 'food',
                            'tag_id' => $this->tag->id,
                            'description' => 'Family dinner',
                            'date' => '2026-04-26',
                        ]),
                    ],
                ],
            ],
        ]),
    ]);

    Sanctum::actingAs($this->user);

    $this->postJson('/api/v1/expenses/voice', ['text' => 'family dinner 12 marks'])
        ->assertSuccessful()
        ->assertJsonPath('data.suggested_tag_id', $this->tag->id);
});
