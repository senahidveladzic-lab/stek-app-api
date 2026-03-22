<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Household extends Model
{
    /** @use HasFactory<\Database\Factories\HouseholdFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id',
        'default_currency',
        'max_members',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return HasMany<User, $this>
     */
    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<Expense, $this>
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * @return HasMany<HouseholdInvitation, $this>
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(HouseholdInvitation::class);
    }

    /**
     * @return HasMany<Budget, $this>
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
