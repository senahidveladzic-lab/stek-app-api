<?php

namespace App\Services;

use App\Models\GlobalCorrection;
use App\Models\VoiceCorrection;

class VoiceCorrectionService
{
    /**
     * Log a correction if the user changed anything before saving.
     *
     * @param  array{description: string, category_key: string, amount: float}  $original
     * @param  array{description: string, category_key: string, amount: float}  $corrected
     */
    public function log(int $userId, array $original, array $corrected, ?string $transcript = null): void
    {
        $descriptionChanged = $original['description'] !== $corrected['description'];
        $categoryChanged = $original['category_key'] !== $corrected['category_key'];

        if (! $descriptionChanged && ! $categoryChanged) {
            return;
        }

        VoiceCorrection::create([
            'user_id' => $userId,
            'whisper_transcript' => $transcript,
            'original_description' => $original['description'],
            'original_category_key' => $original['category_key'],
            'original_amount' => $original['amount'],
            'corrected_description' => $corrected['description'],
            'corrected_category_key' => $corrected['category_key'],
            'corrected_amount' => $corrected['amount'],
        ]);
    }

    /**
     * Return this user's recent distinct corrections as few-shot prompt examples.
     *
     * @return list<array{original_description: string, corrected_description: string, corrected_category_key: string}>
     */
    public function userExamples(int $userId, int $limit = 10): array
    {
        return VoiceCorrection::where('user_id', $userId)
            ->whereNotNull('original_description')
            ->whereNotNull('corrected_description')
            ->where('original_description', '!=', 'corrected_description')
            ->latest()
            ->limit($limit)
            ->get(['original_description', 'corrected_description', 'corrected_category_key'])
            ->unique('corrected_description')
            ->values()
            ->map(fn ($c) => [
                'original_description' => $c->original_description,
                'corrected_description' => $c->corrected_description,
                'corrected_category_key' => $c->corrected_category_key,
            ])
            ->all();
    }

    /**
     * Return active globally promoted corrections as few-shot prompt examples.
     *
     * @return list<array{corrected_description: string, corrected_category_key: string}>
     */
    public function globalExamples(int $limit = 20): array
    {
        return GlobalCorrection::where('is_active', true)
            ->orderByDesc('frequency')
            ->limit($limit)
            ->get(['corrected_description', 'corrected_category_key'])
            ->map(fn ($c) => [
                'corrected_description' => $c->corrected_description,
                'corrected_category_key' => $c->corrected_category_key,
            ])
            ->all();
    }
}
