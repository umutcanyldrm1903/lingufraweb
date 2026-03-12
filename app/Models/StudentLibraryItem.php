<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentLibraryItem extends Model
{
    protected $fillable = [
        'instructor_id',
        'student_id',
        'category',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
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
