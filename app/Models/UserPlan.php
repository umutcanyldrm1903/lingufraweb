<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPlan extends Model
{
    protected $fillable = [
        'user_id',
        'assigned_instructor_id',
        'plan_key',
        'plan_title',
        'lesson_duration',
        'lessons_total',
        'lessons_remaining',
        'cancel_total',
        'cancel_remaining',
        'starts_at',
        'ends_at',
        'last_order_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'lesson_duration' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedInstructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_instructor_id');
    }
}
