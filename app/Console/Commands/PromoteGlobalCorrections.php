<?php

namespace App\Console\Commands;

use App\Models\GlobalCorrection;
use App\Models\VoiceCorrection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PromoteGlobalCorrections extends Command
{
    protected $signature = 'corrections:promote {--threshold=5 : Minimum distinct users required}';

    protected $description = 'Promote voice corrections made by multiple users to global corrections';

    public function handle(): int
    {
        $threshold = (int) $this->option('threshold');

        $candidates = VoiceCorrection::query()
            ->select([
                'corrected_description',
                'corrected_category_key',
                DB::raw('COUNT(DISTINCT user_id) as user_count'),
            ])
            ->whereNotNull('corrected_description')
            ->whereNotNull('corrected_category_key')
            ->where(function ($q) {
                $q->whereColumn('corrected_description', '!=', 'original_description')
                    ->orWhereColumn('corrected_category_key', '!=', 'original_category_key');
            })
            ->groupBy('corrected_description', 'corrected_category_key')
            ->having('user_count', '>=', $threshold)
            ->get();

        $promoted = 0;
        $updated = 0;

        foreach ($candidates as $candidate) {
            $existing = GlobalCorrection::where('corrected_description', $candidate->corrected_description)
                ->where('corrected_category_key', $candidate->corrected_category_key)
                ->first();

            if ($existing) {
                $existing->update([
                    'frequency' => $candidate->user_count,
                ]);
                $updated++;
            } else {
                GlobalCorrection::create([
                    'corrected_description' => $candidate->corrected_description,
                    'corrected_category_key' => $candidate->corrected_category_key,
                    'frequency' => $candidate->user_count,
                    'is_active' => true,
                    'promoted_at' => now(),
                ]);
                $promoted++;
            }
        }

        $this->info("Promoted: {$promoted} new | Updated: {$updated} existing | Threshold: {$threshold} users");

        return self::SUCCESS;
    }
}
