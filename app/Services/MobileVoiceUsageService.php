<?php

namespace App\Services;

use App\Exceptions\DailyVoiceLimitExceededException;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class MobileVoiceUsageService
{
    public const DAILY_LIMIT = 2;

    public function consume(User $user): void
    {
        if ($user->hasBillingAccess()) {
            return;
        }

        $key = sprintf('mobile_voice_usage:%d:%s', $user->id, now()->toDateString());
        $expiresAt = now()->endOfDay()->addSecond();

        Cache::add($key, 0, $expiresAt);

        $used = Cache::increment($key);
        if (! is_int($used)) {
            $used = (int) Cache::get($key, 1);
        }

        if ($used > self::DAILY_LIMIT) {
            throw new DailyVoiceLimitExceededException;
        }
    }
}
