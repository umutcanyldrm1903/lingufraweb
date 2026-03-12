<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentHomeworkSubmission extends Model
{
    protected $fillable = [
        'student_homework_id',
        'student_id',
        'submission_path',
        'submission_name',
        'note',
        'submitted_at',
        'status',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function homework(): BelongsTo
    {
        return $this->belongsTo(StudentHomework::class, 'student_homework_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
