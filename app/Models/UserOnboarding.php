<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOnboarding extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'learning_goal',
        'instructor_preference',
        'availability',
        'english_level',
        'lesson_place',
        'student_type',
        'goal',
        'level',
        'frequency',
        'details',
        'start_when',
        'birth_date',
        'referral_code',
        'referred_by_user_id',
        'heard_from',
        'marketing_consent',
        'terms_accepted_at',
    ];

    protected $casts = [
        'availability' => 'array',
        'birth_date' => 'date',
        'marketing_consent' => 'boolean',
        'terms_accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
