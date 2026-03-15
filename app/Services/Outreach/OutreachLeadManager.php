<?php

namespace App\Services\Outreach;

use App\Models\OutreachCampaign;
use App\Models\OutreachLead;

class OutreachLeadManager
{
    public function upsertFromLusha(OutreachCampaign $campaign, array $contact, string $status = 'imported'): OutreachLead
    {
        $email = isset($contact['email']) && filter_var($contact['email'], FILTER_VALIDATE_EMAIL)
            ? strtolower($contact['email'])
            : null;

        [$firstName, $lastName] = $this->resolveNameParts(
            $contact['full_name'] ?? null,
            $contact['first_name'] ?? null,
            $contact['last_name'] ?? null
        );

        $match = ['campaign_id' => $campaign->id];

        if (! empty($contact['contact_id'])) {
            $match['contact_id'] = $contact['contact_id'];
        } elseif ($email) {
            $match['email'] = $email;
        } else {
            $match['full_name'] = $contact['full_name'] ?? 'unknown';
            $match['company_name'] = $contact['company_name'] ?? null;
        }

        $lead = OutreachLead::firstOrNew($match);

        $lead->fill([
            'status' => $status,
            'source' => 'lusha',
            'request_id' => $contact['request_id'] ?? $lead->request_id,
            'contact_id' => $contact['contact_id'] ?? $lead->contact_id,
            'external_id' => $contact['external_id'] ?? $lead->external_id,
            'full_name' => $contact['full_name'] ?? trim($firstName . ' ' . $lastName) ?: $lead->full_name,
            'first_name' => $firstName ?: $lead->first_name,
            'last_name' => $lastName ?: $lead->last_name,
            'email' => $email ?: $lead->email,
            'company_name' => $contact['company_name'] ?? $lead->company_name,
            'job_title' => $contact['job_title'] ?? $lead->job_title,
            'linkedin_url' => $contact['linkedin_url'] ?? $lead->linkedin_url,
            'location' => $contact['location'] ?? $lead->location,
            'source_metadata' => $contact['source_metadata'] ?? $lead->source_metadata,
        ]);

        if ($status === 'enriched') {
            $lead->last_enriched_at = now();
            $lead->enrichment_payload = $contact['source_metadata'] ?? $contact;
        }

        $lead->save();

        return $lead;
    }

    protected function resolveNameParts(?string $fullName, ?string $firstName, ?string $lastName): array
    {
        $firstName = trim((string) $firstName);
        $lastName = trim((string) $lastName);

        if ($firstName !== '' || $lastName !== '') {
            return [$firstName ?: null, $lastName ?: null];
        }

        $fullName = trim((string) $fullName);

        if ($fullName === '') {
            return [null, null];
        }

        $parts = preg_split('/\s+/', $fullName) ?: [];
        $first = $parts[0] ?? null;
        $last = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : null;

        return [$first, $last];
    }
}
