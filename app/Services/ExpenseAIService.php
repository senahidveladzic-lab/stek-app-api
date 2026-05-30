<?php

namespace App\Services;

use App\Exceptions\ExpenseParseException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpenseAIService
{
    private const VALID_CATEGORIES = [
        'food', 'groceries', 'transport', 'entertainment',
        'bills', 'shopping', 'health', 'education', 'cafe', 'other',
    ];

    /**
     * @param  list<array{id: int, name: string, color: string}>  $tags
     * @param  list<array{original_description: string, corrected_description: string, corrected_category_key: string}>  $userCorrections
     * @param  list<array{corrected_description: string, corrected_category_key: string}>  $globalCorrections
     * @return array{amount: float, currency: string, category_key: string, description: string, date: string, suggested_tag_id: int|null}
     */
    public function parse(string $userText, string $locale, string $defaultCurrency, array $tags = [], array $userCorrections = [], array $globalCorrections = []): array
    {
        $prompt = $this->buildPrompt($userText, $locale, $defaultCurrency, $tags, $userCorrections, $globalCorrections);

        Log::debug('[ExpenseAI] raw input', ['text' => $userText, 'locale' => $locale, 'currency' => $defaultCurrency]);
        Log::debug('[ExpenseAI] prompt sent', ['prompt' => $prompt]);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.config('services.openai.api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => config('services.openai.chat_model'),
            'max_tokens' => 200,
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ]);

        if ($response->failed()) {
            Log::error('[ExpenseAI] API error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new ExpenseParseException('AI API request failed: '.$response->status());
        }

        $text = $response->json('choices.0.message.content', '');

        Log::debug('[ExpenseAI] raw AI response', ['response' => $text]);

        return $this->parseResponse($text, array_column($tags, 'id'));
    }

    /**
     * @param  list<array{id: int, name: string, color: string}>  $tags
     * @param  list<array{original_description: string, corrected_description: string, corrected_category_key: string}>  $userCorrections
     * @param  list<array{corrected_description: string, corrected_category_key: string}>  $globalCorrections
     */
    public function buildPrompt(string $userText, string $locale, string $defaultCurrency, array $tags = [], array $userCorrections = [], array $globalCorrections = []): string
    {
        $promptPath = resource_path("prompts/expense_parse/{$locale}.txt");

        if (! file_exists($promptPath)) {
            $promptPath = resource_path('prompts/expense_parse/en.txt');
        }

        $template = file_get_contents($promptPath);

        $prompt = str_replace(
            ['{user_text}', '{default_currency}', '{today_date}', '{tags}'],
            [
                $userText,
                $defaultCurrency,
                now()->format('Y-m-d'),
                json_encode($tags, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ],
            $template,
        );

        $correctionLines = $this->buildCorrectionLines($userCorrections, $globalCorrections);

        if ($correctionLines !== '') {
            $prompt = str_replace(
                'Korisnikov tekst:',
                $correctionLines."\n\nKorisnikov tekst:",
                $prompt,
            );
            // Fallback for English prompt
            $prompt = str_replace(
                "User's text:",
                $correctionLines."\n\nUser's text:",
                $prompt,
            );
        }

        return $prompt;
    }

    /**
     * @param  list<array{original_description: string, corrected_description: string, corrected_category_key: string}>  $userCorrections
     * @param  list<array{corrected_description: string, corrected_category_key: string}>  $globalCorrections
     */
    private function buildCorrectionLines(array $userCorrections, array $globalCorrections): string
    {
        $lines = [];

        foreach ($globalCorrections as $c) {
            $lines[] = sprintf(
                '- "%s" → description: "%s", category: %s',
                $c['corrected_description'],
                $c['corrected_description'],
                $c['corrected_category_key'],
            );
        }

        foreach ($userCorrections as $c) {
            $lines[] = sprintf(
                '- "%s" → description: "%s", category: %s',
                $c['original_description'],
                $c['corrected_description'],
                $c['corrected_category_key'],
            );
        }

        if (empty($lines)) {
            return '';
        }

        return "Naučene korekcije (primijeni isti ispravak na sličan tekst):\n".implode("\n", $lines);
    }

    /**
     * @param  list<int>|null  $allowedTagIds
     * @return array{amount: float, currency: string, category_key: string, description: string, date: string, suggested_tag_id: int|null}
     */
    public function parseResponse(string $text, ?array $allowedTagIds = null): array
    {
        $text = trim($text);
        $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
        $text = preg_replace('/\s*```$/i', '', $text);

        $data = json_decode(trim($text), true);

        if (! is_array($data)) {
            throw new ExpenseParseException('Failed to parse AI response as JSON.');
        }

        $amount = (float) ($data['amount'] ?? 0);
        if ($amount < 0) {
            $amount = 0;
        }

        $categoryKey = $data['category_key'] ?? 'other';
        if (! in_array($categoryKey, self::VALID_CATEGORIES)) {
            $categoryKey = 'other';
        }

        $currency = strtoupper($data['currency'] ?? 'BAM');
        if (strlen($currency) !== 3) {
            $currency = 'BAM';
        }

        $date = $data['date'] ?? now()->format('Y-m-d');
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = now()->format('Y-m-d');
        }

        $description = trim((string) ($data['description'] ?? ''));
        $description = preg_replace('/\s+/', ' ', $description) ?? '';
        $description = mb_substr($description, 0, 255);

        $suggestedTagId = $this->parseTagId($data['tag_id'] ?? null, $allowedTagIds);

        return [
            'amount' => $amount,
            'currency' => $currency,
            'category_key' => $categoryKey,
            'description' => $description,
            'date' => $date,
            'suggested_tag_id' => $suggestedTagId,
        ];
    }

    /**
     * @param  list<int>|null  $allowedTagIds
     */
    private function parseTagId(mixed $tagId, ?array $allowedTagIds): ?int
    {
        if ($tagId === null || $tagId === '') {
            return null;
        }

        $parsedTagId = filter_var($tagId, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($parsedTagId === false) {
            return null;
        }

        if ($allowedTagIds !== null && ! in_array($parsedTagId, $allowedTagIds, true)) {
            return null;
        }

        return $parsedTagId;
    }
}
