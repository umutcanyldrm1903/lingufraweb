<?php

namespace App\Services\Outreach;

use App\Mail\OutreachMail;
use App\Models\OutreachCampaign;
use App\Models\OutreachLead;
use App\Models\OutreachMessage;
use App\Models\OutreachSuppression;
use App\Traits\MailSenderTrait;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;

class OutreachAutomationService
{
    use MailSenderTrait;

    public function __construct(
        protected OutreachAiComposer $aiComposer,
        protected OutreachDeliveryGuard $deliveryGuard
    ) {
    }

    public function generateMessageForLead(OutreachLead $lead, bool $refresh = false): OutreachMessage
    {
        $lead->loadMissing('campaign');
        $campaign = $lead->campaign;

        if (! $lead->email || ! filter_var($lead->email, FILTER_VALIDATE_EMAIL)) {
            $lead->forceFill([
                'status' => 'invalid',
                'invalid_email_at' => now(),
            ])->save();

            throw new RuntimeException("Lead {$lead->id} has no valid email.");
        }

        if ($this->isSuppressed($lead->email)) {
            $lead->forceFill([
                'status' => 'suppressed',
                'opted_out_at' => now(),
            ])->save();

            throw new RuntimeException("Lead {$lead->email} is suppressed.");
        }

        $message = OutreachMessage::firstOrNew([
            'campaign_id' => $campaign->id,
            'lead_id' => $lead->id,
        ]);

        if (! $message->uuid) {
            $message->uuid = (string) Str::ulid();
        }

        if (! $message->unsubscribe_token) {
            $message->unsubscribe_token = Str::random(40);
        }

        if ($message->exists && $message->sent_at && ! $refresh) {
            throw new RuntimeException("Lead {$lead->id} already has a sent message.");
        }

        $payload = $this->aiComposer->compose($campaign, $lead);
        $content = $this->renderContent($campaign, $lead, $message, $payload);
        $status = $campaign->require_approval ? 'generated' : 'approved';

        $message->fill([
            'status' => $status,
            'ai_model' => $payload['ai_model'] ?? null,
            'subject' => $content['subject'],
            'body_text' => $content['body_text'],
            'body_html' => $content['body_html'],
            'preview_payload' => [
                'opening_line' => $payload['opening_line'],
                'body_lines' => $payload['body_lines'],
                'cta' => $payload['cta'],
            ],
            'risk_flags' => $payload['risk_flags'] ?? [],
            'prompt_version' => OutreachAiComposer::PROMPT_VERSION,
            'generation_error' => $payload['generation_error'],
            'generated_at' => now(),
            'approved_at' => $campaign->require_approval ? $message->approved_at : now(),
            'scheduled_at' => null,
            'failed_at' => null,
            'failure_reason' => null,
        ]);
        $message->save();

        $lead->forceFill(['status' => 'ready'])->save();

        return $message->fresh(['campaign', 'lead']);
    }

    public function approveMessage(OutreachMessage $message): OutreachMessage
    {
        $message->forceFill([
            'status' => 'approved',
            'approved_at' => now(),
        ])->save();

        return $message->fresh(['campaign', 'lead']);
    }

    public function sendMessage(OutreachMessage $message, bool $force = false): OutreachMessage
    {
        $message->loadMissing('campaign', 'lead');
        $lead = $message->lead;

        if (! $lead->email || ! filter_var($lead->email, FILTER_VALIDATE_EMAIL)) {
            $lead->forceFill([
                'status' => 'invalid',
                'invalid_email_at' => now(),
            ])->save();

            throw new RuntimeException("Lead {$lead->id} has no valid email.");
        }

        if ($this->isSuppressed($lead->email)) {
            $this->markSuppressedMessage($message, 'suppressed before send');

            throw new RuntimeException("Lead {$lead->email} is suppressed.");
        }

        if (! $message->subject || ! $message->body_text || ! $message->body_html) {
            throw new RuntimeException("Message {$message->id} has not been generated yet.");
        }

        if ($message->campaign->require_approval && ! $force && $message->status !== 'approved') {
            throw new RuntimeException("Message {$message->id} is not approved.");
        }

        $deliveryState = $this->deliveryGuard->evaluate($message);

        if (! $deliveryState['allowed']) {
            $message->forceFill([
                'status' => 'approved',
                'scheduled_at' => $deliveryState['next_attempt_at'],
                'failure_reason' => $deliveryState['reason'],
            ])->save();

            return $message->fresh(['campaign', 'lead']);
        }

        if (! self::setMailConfig()) {
            throw new RuntimeException('Mail configuration is not ready.');
        }

        $message->forceFill([
            'status' => 'sending',
            'failure_reason' => null,
        ])->save();

        try {
            $sentMessage = Mail::to($lead->email)->send(new OutreachMail($message));

            $message->forceFill([
                'status' => 'sent',
                'provider_message_id' => $sentMessage?->getMessageId(),
                'sent_at' => now(),
                'scheduled_at' => null,
                'failed_at' => null,
                'failure_reason' => null,
            ])->save();

            $lead->forceFill(['status' => 'sent'])->save();
        } catch (\Throwable $throwable) {
            $message->forceFill([
                'status' => 'failed',
                'failed_at' => now(),
                'failure_reason' => Str::limit($throwable->getMessage(), 1000),
            ])->save();

            throw $throwable;
        }

        return $message->fresh(['campaign', 'lead']);
    }

    public function suppressEmail(string $email, string $reason, string $source = 'system', ?OutreachCampaign $campaign = null, ?string $notes = null): OutreachSuppression
    {
        $suppression = OutreachSuppression::updateOrCreate(
            ['email' => strtolower($email)],
            [
                'campaign_id' => $campaign?->id,
                'reason' => $reason,
                'source' => $source,
                'notes' => $notes,
                'suppressed_at' => now(),
            ]
        );

        OutreachLead::where('email', strtolower($email))->update([
            'status' => 'suppressed',
            'opted_out_at' => now(),
        ]);

        OutreachMessage::whereHas('lead', function ($query) use ($email) {
            $query->where('email', strtolower($email));
        })->whereNull('sent_at')->update([
            'status' => 'suppressed',
            'failure_reason' => $reason,
        ]);

        return $suppression;
    }

    public function isSuppressed(?string $email): bool
    {
        if (! $email) {
            return false;
        }

        return OutreachSuppression::where('email', strtolower($email))->exists();
    }

    protected function markSuppressedMessage(OutreachMessage $message, string $reason): void
    {
        $message->forceFill([
            'status' => 'suppressed',
            'failure_reason' => $reason,
        ])->save();
    }

    protected function renderContent(OutreachCampaign $campaign, OutreachLead $lead, OutreachMessage $message, array $payload): array
    {
        $paragraphs = collect([
            trim((string) ($payload['opening_line'] ?? '')),
            ...collect($payload['body_lines'] ?? [])->map(fn ($line) => trim((string) $line))->all(),
            trim((string) ($payload['cta'] ?? '')),
        ])->filter()->values()->all();

        $signatureText = trim((string) $campaign->signature_text);
        $unsubscribeUrl = $message->unsubscribeUrl();
        $unsubscribeMailto = trim((string) ($campaign->unsubscribe_mailto ?: config('mail.from.address')));
        $footerText = $this->footerText($campaign, $unsubscribeUrl, $unsubscribeMailto);
        $footerHtml = $this->footerHtml($campaign, $unsubscribeUrl, $unsubscribeMailto);

        $textParts = $paragraphs;

        if ($signatureText !== '') {
            $textParts[] = $signatureText;
        }

        $textParts[] = $footerText;

        $htmlParts = collect($paragraphs)
            ->map(fn ($paragraph) => '<p>' . e($paragraph) . '</p>')
            ->all();

        if (trim((string) $campaign->signature_html) !== '') {
            $htmlParts[] = (string) $campaign->signature_html;
        } elseif ($signatureText !== '') {
            foreach (preg_split("/\r\n|\n|\r/", $signatureText) ?: [] as $line) {
                $line = trim($line);

                if ($line !== '') {
                    $htmlParts[] = '<p>' . e($line) . '</p>';
                }
            }
        }

        $htmlParts[] = $footerHtml;

        return [
            'subject' => Str::limit(trim((string) ($payload['subject'] ?? 'Kisa bir tanisma')), 110, ''),
            'body_text' => implode("\n\n", array_filter($textParts)),
            'body_html' => implode("\n", array_filter($htmlParts)),
        ];
    }

    protected function footerText(OutreachCampaign $campaign, ?string $unsubscribeUrl, string $unsubscribeMailto): string
    {
        $isTurkish = str_starts_with(strtolower((string) $campaign->language), 'tr');

        if ($unsubscribeUrl) {
            return $isTurkish
                ? "Bu e-postalari almak istemiyorsaniz ayrilabilirsiniz: {$unsubscribeUrl}"
                : "If you do not want these emails, you can unsubscribe here: {$unsubscribeUrl}";
        }

        return $isTurkish
            ? "Bu e-postalari almak istemiyorsaniz {$unsubscribeMailto} adresine yazabilirsiniz."
            : "If you do not want these emails, reply or write to {$unsubscribeMailto}.";
    }

    protected function footerHtml(OutreachCampaign $campaign, ?string $unsubscribeUrl, string $unsubscribeMailto): string
    {
        $isTurkish = str_starts_with(strtolower((string) $campaign->language), 'tr');

        if ($unsubscribeUrl) {
            $label = $isTurkish ? 'ayrilmak icin tiklayin' : 'unsubscribe';

            return '<p style="font-size:12px;color:#6b7280;">'
                . ($isTurkish ? 'Bu e-postalari almak istemiyorsaniz ' : 'If you do not want these emails, ')
                . '<a href="' . e($unsubscribeUrl) . '">' . e($label) . '</a>.'
                . '</p>';
        }

        return '<p style="font-size:12px;color:#6b7280;">'
            . ($isTurkish ? 'Bu e-postalari almak istemiyorsaniz ' : 'If you do not want these emails, ')
            . e($unsubscribeMailto)
            . '</p>';
    }
}
