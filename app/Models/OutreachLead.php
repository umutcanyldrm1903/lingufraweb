<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutreachLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'status',
        'source',
        'contact_id',
        'request_id',
        'external_id',
        'full_name',
        'first_name',
        'last_name',
        'email',
        'company_name',
        'job_title',
        'linkedin_url',
        'location',
        'source_metadata',
        'enrichment_payload',
        'last_enriched_at',
        'opted_out_at',
        'invalid_email_at',
    ];

    protected $casts = [
        'source_metadata' => 'array',
        'enrichment_payload' => 'array',
        'last_enriched_at' => 'datetime',
        'opted_out_at' => 'datetime',
        'invalid_email_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(OutreachCampaign::class, 'campaign_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(OutreachMessage::class, 'lead_id');
    }
}
