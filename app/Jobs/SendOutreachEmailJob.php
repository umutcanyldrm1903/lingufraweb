<?php

namespace App\Jobs;

use App\Models\OutreachMessage;
use App\Services\Outreach\OutreachAutomationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOutreachEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $messageId,
        public bool $force = false
    ) {
    }

    public function handle(OutreachAutomationService $outreachAutomationService): void
    {
        $message = OutreachMessage::findOrFail($this->messageId);

        $outreachAutomationService->sendMessage($message, $this->force);
    }
}
