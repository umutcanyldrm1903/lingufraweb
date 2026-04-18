<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobileAnalyticsEvent extends Model
{
    protected $fillable = [
        'user_id',
        'source',
        'name',
        'event_time',
        'segment',
        'experiment',
        'ip',
        'properties',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
