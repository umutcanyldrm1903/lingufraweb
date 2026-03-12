<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPlan extends Model
{
    protected $fillable = [
        'key',
        'title',
        'display_title',
        'label',
        'subtitle',
        'tagline',
        'duration_months',
        'lesson_duration',
        'lessons_total',
        'cancel_total',
        'old_price',
        'price',
        'featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'duration_months' => 'integer',
        'lesson_duration' => 'integer',
        'lessons_total' => 'integer',
        'cancel_total' => 'integer',
        'old_price' => 'decimal:2',
        'price' => 'decimal:2',
        'featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
