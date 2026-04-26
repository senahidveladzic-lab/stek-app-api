<?php

use App\Models\User;
use App\Services\ExpenseAIService;
use Illuminate\Support\Facades\Http;

it('builds prompt with correct locale template', function () {
    $service = app(ExpenseAIService::class);
    $prompt = $service->buildPrompt('kafa 3 marke', 'bs', 'BAM');

    expect($prompt)
        ->toContain('kafa 3 marke')
        ->toContain('BAM')
        ->toContain(now()->format('Y-m-d'))
        ->toContain('gramatički ispravan opis troška')
        ->toContain('Kafa i sladoled u kafiću');
});

it('falls back to english template for unknown locale', function () {
    $service = app(ExpenseAIService::class);
    $prompt = $service->buildPrompt('coffee 5 dollars', 'fr', 'USD');

    expect($prompt)
        ->toContain('coffee 5 dollars')
        ->toContain('USD')
        ->toContain('Do not copy noisy speech-to-text literally');
});

it('voice endpoint calls AI and returns parsed data', function () {
    Http::fake([
        'api.openai.com/v1/chat/*' => Http::response([
            'choices' => [
                [
                    'message' => [
                        'content' => json_encode([
                            'amount' => 3,
                            'currency' => 'BAM',
                            'category_key' => 'cafe',
                            'description' => 'Kafa',
                            'date' => '2026-03-06',
                        ]),
                    ],
                ],
            ],
        ]),
    ]);

    $user = User::factory()->withInternalAccess()->create();

    $response = $this->actingAs($user)
        ->postJson('/expenses/voice', ['text' => 'kafa 3 marke']);

    $response->assertSuccessful()
        ->assertJsonPath('data.amount', 3)
        ->assertJsonPath('data.category_key', 'cafe');
});

it('voice endpoint returns error on AI failure', function () {
    Http::fake([
        'api.openai.com/v1/chat/*' => Http::response([], 500),
    ]);

    $user = User::factory()->withInternalAccess()->create();

    $response = $this->actingAs($user)
        ->postJson('/expenses/voice', ['text' => 'something']);

    $response->assertStatus(422);
});
