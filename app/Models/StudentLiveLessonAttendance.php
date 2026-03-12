<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentLiveLessonAttendance extends Model
{
    protected $fillable = [
        'student_id',
        'student_live_lesson_id',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(StudentLiveLesson::class, 'student_live_lesson_id');
    }
}
