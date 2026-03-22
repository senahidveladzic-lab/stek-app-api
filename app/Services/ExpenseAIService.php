<?php

namespace App\Services;

use App\Exceptions\ExpenseParseException;
use Illuminate\Support\Facades\Http;

class ExpenseAIService
{
    private const VALID_CATEGORIES = [
        'restaurant', 'groceries', 'transport', 'entertainment',
        'bills', 'shopping', 'health', 'education', 'cafe', 'other',
    ];

    /**
     * @return array{amount: float, currency: string, category_key: string, merchant: ?string, description: string, date: string}
     */
    public function parse(string $userText, string $locale, string $defaultCurrency): array
    {
        $prompt = $this->buildPrompt($userText, $locale, $defaultCurrency);

        $response = Http::withHeaders([
            'x-api-key' => config('services.anthropic.api_key'),
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => config('services.anthropic.model'),
            'max_tokens' => 200,
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ]);

        if ($response->failed()) {
            throw new ExpenseParseException('AI API request failed: '.$response->status());
        }

        $text = $response->json('content.0.text', '');

        return $this->parseResponse($text);
    }

    public function buildPrompt(string $userText, string $locale, string $defaultCurrency): string
    {
        $promptPath = resource_path("prompts/expense_parse/{$locale}.txt");

        if (! file_exists($promptPath)) {
            $promptPath = resource_path('prompts/expense_parse/en.txt');
        }

        $template = file_get_contents($promptPath);

        return str_replace(
            ['{user_text}', '{default_currency}', '{today_date}'],
            [$userText, $defaultCurrency, now()->format('Y-m-d')],
            $template,
        );
    }

    /**
     * @return array{amount: float, currency: string, category_key: string, merchant: ?string, description: string, date: string}
     */
    public function parseResponse(string $text): array
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

        return [
            'amount' => $amount,
            'currency' => $currency,
            'category_key' => $categoryKey,
            'merchant' => $data['merchant'] ?? null,
            'description' => $data['description'] ?? '',
            'date' => $date,
        ];
    }
}
