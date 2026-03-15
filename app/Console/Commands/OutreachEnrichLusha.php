<?php

namespace App\Console\Commands;

use App\Models\OutreachCampaign;
use App\Models\OutreachLead;
use App\Services\Outreach\LushaClient;
use App\Services\Outreach\OutreachLeadManager;
use Illuminate\Console\Command;

class OutreachEnrichLusha extends Command
{
    protected $signature = 'outreach:enrich-lusha
        {campaignId : Outreach campaign id}
        {--limit=25 : Max number of leads to enrich}';

    protected $description = 'Enrich imported Lusha leads to fetch emails and extra lead data.';

    public function handle(LushaClient $lushaClient, OutreachLeadManager $leadManager): int
    {
        $campaign = OutreachCampaign::findOrFail((int) $this->argument('campaignId'));
        $limit = max(1, (int) $this->option('limit'));

        $leads = OutreachLead::where('campaign_id', $campaign->id)
            ->whereNull('email')
            ->whereNotNull('request_id')
            ->whereNotNull('contact_id')
            ->limit($limit)
            ->get();

        $success = 0;
        $failed = 0;

        foreach ($leads as $lead) {
            try {
                $contact = $lushaClient->enrichLead($lead);

                if ($contact === []) {
                    $failed++;
                    continue;
                }

                $leadManager->upsertFromLusha($campaign, $contact, 'enriched');
                $success++;
            } catch (\Throwable $throwable) {
                $lead->forceFill([
                    'status' => 'enrich_failed',
                    'enrichment_payload' => [
                        'error' => $throwable->getMessage(),
                    ],
                ])->save();
                $failed++;
            }
        }

        if ($success > 0) {
            $campaign->forceFill(['status' => 'enriched'])->save();
        }

        $this->info("Enriched: {$success}, failed: {$failed}.");

        return self::SUCCESS;
    }
}
