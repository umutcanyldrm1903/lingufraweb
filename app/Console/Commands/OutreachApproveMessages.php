<?php

namespace App\Console\Commands;

use App\Models\OutreachCampaign;
use App\Models\OutreachMessage;
use App\Services\Outreach\OutreachAutomationService;
use Illuminate\Console\Command;

class OutreachApproveMessages extends Command
{
    protected $signature = 'outreach:approve
        {campaignId : Outreach campaign id}
        {--limit=100 : Max generated messages to approve}';

    protected $description = 'Approve generated outreach drafts for sending.';

    public function handle(OutreachAutomationService $automationService): int
    {
        $campaign = OutreachCampaign::findOrFail((int) $this->argument('campaignId'));
        $limit = max(1, (int) $this->option('limit'));

        $messages = OutreachMessage::where('campaign_id', $campaign->id)
            ->where('status', 'generated')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        foreach ($messages as $message) {
            $automationService->approveMessage($message);
        }

        $campaign->forceFill(['status' => 'approved'])->save();

        $this->info("Approved {$messages->count()} message(s).");

        return self::SUCCESS;
    }
}
