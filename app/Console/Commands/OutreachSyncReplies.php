<?php

namespace App\Console\Commands;

use App\Models\OutreachCampaign;
use App\Services\Outreach\ImapReplySyncService;
use Illuminate\Console\Command;

class OutreachSyncReplies extends Command
{
    protected $signature = 'outreach:sync-replies
        {campaignId? : Optional outreach campaign id}';

    protected $description = 'Sync email replies over IMAP and mark matching outreach messages as replied.';

    public function handle(ImapReplySyncService $imapReplySyncService): int
    {
        $campaignId = $this->argument('campaignId');
        $campaign = $campaignId ? OutreachCampaign::findOrFail((int) $campaignId) : null;

        $result = $imapReplySyncService->sync($campaign);

        $this->info("Searched {$result['searched']} mailbox message(s), matched {$result['matched']} outreach reply/replies.");

        return self::SUCCESS;
    }
}
