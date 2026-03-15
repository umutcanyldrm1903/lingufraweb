<?php

namespace App\Jobs;

use App\Models\OutreachLead;
use App\Services\Outreach\OutreachAutomationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateOutreachMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $leadId,
        public bool $refresh = false
    ) {
    }

    public function handle(OutreachAutomationService $outreachAutomationService): void
    {
        $lead = OutreachLead::findOrFail($this->leadId);

        $outreachAutomationService->generateMessageForLead($lead, $this->refresh);
    }
}
