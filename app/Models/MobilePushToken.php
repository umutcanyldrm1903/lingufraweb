<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MobilePushToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'platform',
        'timezone',
        'locale',
        'reminder_window',
        'reminders_enabled',
        'last_daily_sent_on',
        'last_seen_at',
    ];

    protected $casts = [
        'reminders_enabled' => 'boolean',
        'last_daily_sent_on' => 'date',
        'last_seen_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
