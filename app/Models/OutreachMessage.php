<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class OutreachMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'campaign_id',
        'lead_id',
        'status',
        'ai_model',
        'subject',
        'body_text',
        'body_html',
        'preview_payload',
        'risk_flags',
        'prompt_version',
        'generation_error',
        'generated_at',
        'approved_at',
        'scheduled_at',
        'sent_at',
        'failed_at',
        'replied_at',
        'reply_excerpt',
        'provider_message_id',
        'provider_headers',
        'failure_reason',
        'unsubscribe_token',
    ];

    protected $casts = [
        'preview_payload' => 'array',
        'risk_flags' => 'array',
        'provider_headers' => 'array',
        'generated_at' => 'datetime',
        'approved_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'replied_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (OutreachMessage $message) {
            if (! $message->uuid) {
                $message->uuid = (string) Str::ulid();
            }

            if (! $message->unsubscribe_token) {
                $message->unsubscribe_token = Str::random(40);
            }
        });
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(OutreachCampaign::class, 'campaign_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(OutreachLead::class, 'lead_id');
    }

    public function outboundMessageId(): string
    {
        $domain = null;
        $fromAddress = (string) config('mail.from.address');

        if (str_contains($fromAddress, '@')) {
            $domain = Str::after($fromAddress, '@');
        }

        if (! $domain) {
            $domain = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'localhost';
        }

        return sprintf('outreach.%s@%s', $this->uuid, $domain);
    }

    public function unsubscribeUrl(): ?string
    {
        try {
            return route('outreach.unsubscribe', ['token' => $this->unsubscribe_token]);
        } catch (\Throwable $throwable) {
            return null;
        }
    }
}
