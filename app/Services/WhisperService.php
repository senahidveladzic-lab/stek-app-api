<?php

namespace App\Services;

use App\Exceptions\ExpenseParseException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhisperService
{
    public function transcribe(UploadedFile $audio, string $locale): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.config('services.openai.api_key'),
        ])->attach(
            'file',
            file_get_contents($audio->getRealPath()),
            $audio->getClientOriginalName() ?: 'audio.m4a',
        )->post('https://api.openai.com/v1/audio/transcriptions', [
            'model' => config('services.openai.whisper_model'),
            'language' => $this->resolveLanguage($locale),
            'response_format' => 'text',
        ]);

        if ($response->failed()) {
            throw new ExpenseParseException('Whisper API request failed: '.$response->status());
        }

        $transcript = trim($response->body());

        Log::debug('[Whisper] transcript', ['locale' => $locale, 'text' => $transcript]);

        return $transcript;
    }

    private function resolveLanguage(string $locale): string
    {
        return match ($locale) {
            'bs', 'hr', 'sr' => 'bs',
            default => 'en',
        };
    }
}
