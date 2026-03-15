<?php

namespace App\Console\Commands;

use App\Jobs\GenerateOutreachMessageJob;
use App\Models\OutreachCampaign;
use App\Models\OutreachLead;
use App\Services\Outreach\OutreachAutomationService;
use Illuminate\Console\Command;

class OutreachGenerateMessages extends Command
{
    protected $signature = 'outreach:generate
        {campaignId : Outreach campaign id}
        {--limit=20 : Max leads to process}
        {--refresh : Regenerate existing unsent drafts}
        {--queue : Dispatch generation to queue instead of running inline}';

    protected $description = 'Generate GPT-personalized outreach drafts for leads in a campaign.';

    public function handle(OutreachAutomationService $automationService): int
    {
        $campaign = OutreachCampaign::findOrFail((int) $this->argument('campaignId'));
        $limit = max(1, (int) $this->option('limit'));
        $refresh = (bool) $this->option('refresh');
        $queue = (bool) $this->option('queue');

        $leads = OutreachLead::where('campaign_id', $campaign->id)
            ->whereNotNull('email')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        $processed = 0;
        $failed = 0;

        foreach ($leads as $lead) {
            try {
                if ($queue) {
                    dispatch(new GenerateOutreachMessageJob($lead->id, $refresh));
                } else {
                    $automationService->generateMessageForLead($lead, $refresh);
                }

                $processed++;
            } catch (\Throwable $throwable) {
                $this->warn("Lead #{$lead->id}: {$throwable->getMessage()}");
                $failed++;
            }
        }

        $campaign->forceFill(['status' => $queue ? 'generating' : 'generated'])->save();

        $this->info("Processed: {$processed}, failed: {$failed}.");

        return self::SUCCESS;
    }
}
