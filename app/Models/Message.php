<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model {
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'course_id',
        'body',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function sender(): BelongsTo {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function scopeBetween($query, int $userA, int $userB) {
        return $query->where(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userA)->where('receiver_id', $userB);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userB)->where('receiver_id', $userA);
        });
    }
}
