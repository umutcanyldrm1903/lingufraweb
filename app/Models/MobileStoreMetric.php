<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileStoreMetric extends Model
{
    protected $fillable = [
        'metric_date',
        'store_page_views',
        'installs',
        'trial_starts',
        'trial_conversions',
        'channel',
        'meta',
    ];

    protected $casts = [
        'metric_date' => 'date',
        'meta' => 'array',
    ];
}
