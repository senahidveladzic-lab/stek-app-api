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
        $params = [
            'model' => config('services.openai.whisper_model'),
            'language' => $this->resolveLanguage($locale),
            'response_format' => 'text',
        ];

        $prompt = $this->resolvePrompt($locale);
        if ($prompt !== null) {
            $params['prompt'] = $prompt;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.config('services.openai.api_key'),
        ])->attach(
            'file',
            file_get_contents($audio->getRealPath()),
            $audio->getClientOriginalName() ?: 'audio.m4a',
        )->post('https://api.openai.com/v1/audio/transcriptions', $params);

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
            'bs', 'hr', 'sr' => 'hr',
            default => 'en',
        };
    }

    private function resolvePrompt(string $locale): ?string
    {
        return match ($locale) {
            // Natural Bosnian expense vocabulary — store names, local food, and decimal
            // price format bias Whisper away from misheard words and integer rendering
            // of price expressions like "tri i šezdeset" (should be 3,60, not 63).
            'bs', 'hr', 'sr' => 'Kupovina i troškovi: 3,60 KM, 10,50 maraka, 25,00 BAM, 1,20 eura. '
                . 'Konzum, Bingo, Mercator, Lidl, Studenac, Tommy, Interspar, Kaufland, Robot, Tropic, Amko. '
                . 'Eronet, BH Telecom, Telemach, m:tel. '
                . 'Raiffeisen, UniCredit, Sparkasse, NLB, ProCredit, Addiko, Intesa. '
                . 'Elektroprivreda, EPBiH, ERS, ViK, Sarajevogas. '
                . 'Glovo, Wolt, Donesi, Bolt. '
                . 'Burek, ćevapi, pita, sirnica, somun, baklava, klepe, japrak, zeljanica, sarma, ajvar, kajmak, rakija, grah. '
                . 'Kafa, espresso, čaj, kapućino, sok, kolač, žvake, čokolada. '
                . 'Gorivo, benzin, dizel, autoput, parking, taksi. '
                . 'Ljekarna, apoteka, ordinacija, doktor, stomatolog. '
                . 'Struja, kirija, komunalije, rata, račun, internet, telefon.',
            default => null,
        };
    }
}
