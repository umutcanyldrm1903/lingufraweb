<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StudentHomework extends Model
{
    protected $fillable = [
        'instructor_id',
        'student_id',
        'title',
        'description',
        'due_at',
        'attachment_path',
        'attachment_name',
        'status',
    ];

    protected $casts = [
        'due_at' => 'datetime',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function submission(): HasOne
    {
        return $this->hasOne(StudentHomeworkSubmission::class, 'student_homework_id');
    }
}
