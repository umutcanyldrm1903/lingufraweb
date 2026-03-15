<?php

namespace App\Console\Commands;

use App\Jobs\SendOutreachEmailJob;
use App\Models\OutreachCampaign;
use App\Models\OutreachMessage;
use App\Services\Outreach\OutreachAutomationService;
use Illuminate\Console\Command;

class OutreachSendMessages extends Command
{
    protected $signature = 'outreach:send
        {campaignId : Outreach campaign id}
        {--limit=10 : Max messages to send}
        {--force : Send generated messages without approval}
        {--queue : Dispatch send jobs to queue instead of sending inline}
        {--dry-run : Show candidates but do not send}';

    protected $description = 'Send approved outreach messages through the configured SMTP mailbox.';

    public function handle(OutreachAutomationService $automationService): int
    {
        $campaign = OutreachCampaign::findOrFail((int) $this->argument('campaignId'));
        $limit = max(1, (int) $this->option('limit'));
        $force = (bool) $this->option('force');
        $queue = (bool) $this->option('queue');
        $dryRun = (bool) $this->option('dry-run');

        $statuses = $force ? ['generated', 'approved'] : ['approved'];

        $messages = OutreachMessage::with(['lead'])
            ->where('campaign_id', $campaign->id)
            ->whereIn('status', $statuses)
            ->orderBy('id')
            ->limit($limit)
            ->get();

        if ($dryRun) {
            foreach ($messages as $message) {
                $this->line("#{$message->id} {$message->lead?->email} {$message->subject}");
            }

            $this->info("Dry run listed {$messages->count()} candidate(s).");

            return self::SUCCESS;
        }

        $sent = 0;
        $deferred = 0;
        $failed = 0;

        foreach ($messages as $message) {
            try {
                if ($queue) {
                    dispatch(new SendOutreachEmailJob($message->id, $force));
                    $sent++;
                    continue;
                }

                $updated = $automationService->sendMessage($message, $force);

                if ($updated->status === 'sent') {
                    $sent++;
                } else {
                    $deferred++;
                }
            } catch (\Throwable $throwable) {
                $this->warn("Message #{$message->id}: {$throwable->getMessage()}");
                $failed++;
            }
        }

        $campaign->forceFill(['status' => $queue ? 'sending' : 'sent'])->save();

        $this->info("Sent/queued: {$sent}, deferred: {$deferred}, failed: {$failed}.");

        return self::SUCCESS;
    }
}
