<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentLiveLesson extends Model
{
    protected $fillable = [
        'instructor_id',
        'student_id',
        'title',
        'start_time',
        'meeting_id',
        'password',
        'join_url',
        'type',
        'status',
        'cancelled_by',
        'cancelled_reason',
        'cancelled_at',
        'ended_at',
        'instructor_summary',
        'instructor_summary_written_at',
        'student_rating',
        'student_review',
        'rated_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'cancelled_at' => 'datetime',
        'ended_at' => 'datetime',
        'instructor_summary_written_at' => 'datetime',
        'rated_at' => 'datetime',
        'student_rating' => 'integer',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
