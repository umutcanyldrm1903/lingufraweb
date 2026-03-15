<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutreachCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'name',
        'status',
        'company_name',
        'company_website',
        'product_name',
        'language',
        'audience_summary',
        'offer_summary',
        'tone',
        'prompt_preamble',
        'signature_text',
        'signature_html',
        'unsubscribe_mailto',
        'timezone',
        'daily_send_limit',
        'hourly_send_limit',
        'min_delay_seconds',
        'send_start_hour',
        'send_end_hour',
        'require_approval',
        'last_lusha_payload',
        'notes',
    ];

    protected $casts = [
        'require_approval' => 'boolean',
        'daily_send_limit' => 'integer',
        'hourly_send_limit' => 'integer',
        'min_delay_seconds' => 'integer',
        'send_start_hour' => 'integer',
        'send_end_hour' => 'integer',
        'last_lusha_payload' => 'array',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(OutreachLead::class, 'campaign_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(OutreachMessage::class, 'campaign_id');
    }

    public function suppressions(): HasMany
    {
        return $this->hasMany(OutreachSuppression::class, 'campaign_id');
    }
}
