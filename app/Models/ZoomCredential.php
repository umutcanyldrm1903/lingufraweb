<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ZoomCredential extends Model
{
    use HasFactory;
    protected $fillable = [
        'instructor_id',
        'client_id',
        'client_secret',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'zoom_user_id',
        'zoom_email',
        'scope',
        'default_meeting_id',
        'default_meeting_password',
        'default_join_url',
        'default_meeting_created_at',
    ];

    function instructor(): BelongsTo {
        return $this->belongsTo(User::class, 'instructor_id', 'id')->withDefault();
    }
}
