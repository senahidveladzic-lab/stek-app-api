<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalCorrection extends Model
{
    protected $fillable = [
        'corrected_description',
        'corrected_category_key',
        'frequency',
        'is_active',
        'promoted_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'promoted_at' => 'datetime',
    ];
}
