<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'household_id',
        'category_id',
        'amount',
        'currency',
        'description',
        'original_text',
        'expense_date',
        'original_amount',
        'original_currency',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'original_amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Household, $this>
     */
    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @param  Builder<Expense>  $query
     * @return Builder<Expense>
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * @param  Builder<Expense>  $query
     * @return Builder<Expense>
     */
    public function scopeForHousehold(Builder $query, ?int $householdId): Builder
    {
        return $query->where('household_id', $householdId);
    }

    /**
     * @param  Builder<Expense>  $query
     * @return Builder<Expense>
     */
    public function scopeInDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('expense_date', [$from, $to]);
    }

    /**
     * @param  Builder<Expense>  $query
     * @return Builder<Expense>
     */
    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * @param  Builder<Expense>  $query
     * @return Builder<Expense>
     */
    public function scopeInTag(Builder $query, int $tagId): Builder
    {
        return $query->whereHas('tags', fn (Builder $tagQuery) => $tagQuery->whereKey($tagId));
    }
}
