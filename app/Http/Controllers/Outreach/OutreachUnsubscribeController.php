<?php

namespace App\Http\Controllers\Outreach;

use App\Http\Controllers\Controller;
use App\Models\OutreachMessage;
use App\Services\Outreach\OutreachAutomationService;
use Illuminate\Http\Response;

class OutreachUnsubscribeController extends Controller
{
    public function __invoke(string $token, OutreachAutomationService $outreachAutomationService): Response
    {
        $message = OutreachMessage::with(['campaign', 'lead'])
            ->where('unsubscribe_token', $token)
            ->firstOrFail();

        $outreachAutomationService->suppressEmail(
            $message->lead->email,
            'unsubscribe link clicked',
            'unsubscribe-link',
            $message->campaign
        );

        return response(
            str_starts_with(strtolower((string) $message->campaign->language), 'tr')
                ? 'E-posta listesinden cikarildiniz.'
                : 'You have been unsubscribed from this email list.',
            200
        );
    }
}
