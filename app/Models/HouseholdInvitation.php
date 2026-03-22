<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HouseholdInvitation extends Model
{
    /** @use HasFactory<\Database\Factories\HouseholdInvitationFactory> */
    use HasFactory;

    protected $fillable = [
        'household_id',
        'email',
        'token',
        'accepted_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Household, $this>
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }
}
