<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyConversionService
{
    /** BAM is pegged to EUR at a fixed rate by law */
    private const BAM_EUR_RATE = 1.95583;

    /**
     * Convert an amount from one currency to another.
     *
     * @return array{converted: float, rate: float}
     */
    public function convert(float $amount, string $from, string $to): array
    {
        if ($from === $to) {
            return ['converted' => $amount, 'rate' => 1.0];
        }

        $rate = $this->getRate($from, $to);

        return [
            'converted' => round($amount * $rate, 2),
            'rate' => $rate,
        ];
    }

    private function getRate(string $from, string $to): float
    {
        $fromIsBAM = strtoupper($from) === 'BAM';
        $toIsBAM = strtoupper($to) === 'BAM';

        // Direct BAM ↔ EUR conversions use the fixed peg
        if ($fromIsBAM && strtoupper($to) === 'EUR') {
            return 1 / self::BAM_EUR_RATE;
        }

        if (strtoupper($from) === 'EUR' && $toIsBAM) {
            return self::BAM_EUR_RATE;
        }

        // BAM → other: convert BAM→EUR, then EUR→target
        if ($fromIsBAM) {
            $bamToEur = 1 / self::BAM_EUR_RATE;
            $eurToTarget = $this->fetchRate('EUR', $to);

            return $bamToEur * $eurToTarget;
        }

        // Other → BAM: convert source→EUR, then EUR→BAM
        if ($toIsBAM) {
            $sourceToEur = $this->fetchRate($from, 'EUR');

            return $sourceToEur * self::BAM_EUR_RATE;
        }

        // No BAM involved — direct API lookup
        return $this->fetchRate($from, $to);
    }

    private function fetchRate(string $from, string $to): float
    {
        $cacheKey = "exchange_rate:{$from}:{$to}";

        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return (float) $cached;
        }

        try {
            $response = Http::timeout(5)
                ->get('https://api.frankfurter.app/latest', [
                    'from' => strtoupper($from),
                    'to' => strtoupper($to),
                ]);

            if ($response->successful()) {
                $rate = (float) $response->json("rates.{$to}");
                Cache::put($cacheKey, $rate, now()->addHours(24));

                return $rate;
            }
        } catch (\Throwable $e) {
            Log::warning("Currency conversion API failed: {$e->getMessage()}", [
                'from' => $from,
                'to' => $to,
            ]);
        }

        // Fallback: check for stale cache
        $stale = Cache::get($cacheKey);
        if ($stale !== null) {
            return (float) $stale;
        }

        Log::warning("No exchange rate available for {$from} → {$to}, returning 1.0");

        return 1.0;
    }
}
