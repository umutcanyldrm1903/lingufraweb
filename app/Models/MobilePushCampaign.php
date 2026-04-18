<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobilePushCampaign extends Model
{
    protected $fillable = [
        'name',
        'segment',
        'title_tr',
        'body_tr',
        'title_en',
        'body_en',
        'deep_link',
        'scheduled_at',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_active' => 'boolean',
        'meta' => 'array',
    ];
}
