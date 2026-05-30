<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoiceCorrection extends Model
{
    protected $fillable = [
        'user_id',
        'whisper_transcript',
        'original_description',
        'original_category_key',
        'original_amount',
        'corrected_description',
        'corrected_category_key',
        'corrected_amount',
    ];

    protected $casts = [
        'original_amount' => 'float',
        'corrected_amount' => 'float',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
