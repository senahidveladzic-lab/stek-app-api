<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    /** @use HasFactory<\Database\Factories\BudgetFactory> */
    use HasFactory;

    protected $fillable = [
        'household_id',
        'category_id',
        'month',
        'amount',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'month' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Household, $this>
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @param  Builder<Budget>  $query
     * @return Builder<Budget>
     */
    public function scopeForHousehold(Builder $query, ?int $householdId): Builder
    {
        return $query->where('household_id', $householdId);
    }

    /**
     * @param  Builder<Budget>  $query
     * @return Builder<Budget>
     */
    public function scopeForMonth(Builder $query, string $month): Builder
    {
        return $query->whereDate('month', $month);
    }

    /**
     * @param  Builder<Budget>  $query
     * @return Builder<Budget>
     */
    public function scopeOverall(Builder $query): Builder
    {
        return $query->whereNull('category_id');
    }

    /**
     * @param  Builder<Budget>  $query
     * @return Builder<Budget>
     */
    public function scopeByCategory(Builder $query): Builder
    {
        return $query->whereNotNull('category_id');
    }
}
