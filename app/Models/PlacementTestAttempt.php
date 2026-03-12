<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlacementTestAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'source',
        'locale',
        'contact_name',
        'contact_email',
        'contact_phone',
        'score',
        'max_score',
        'answered_count',
        'level',
        'recommended_track',
        'answers',
        'meta',
    ];

    protected $casts = [
        'answers' => 'array',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

