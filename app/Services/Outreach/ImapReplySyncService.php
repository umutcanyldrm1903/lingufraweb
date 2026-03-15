<?php

namespace App\Services\Outreach;

use App\Models\OutreachCampaign;
use App\Models\OutreachMessage;
use RuntimeException;

class ImapReplySyncService
{
    public function __construct(
        protected OutreachSettingResolver $settings
    ) {
    }

    public function sync(?OutreachCampaign $campaign = null): array
    {
        if (function_exists('imap_open')) {
            return $this->syncWithNativeImap($campaign);
        }

        return $this->syncWithSocketClient($campaign);
    }

    protected function syncWithNativeImap(?OutreachCampaign $campaign = null): array
    {
        $mailbox = $this->mailboxString();
        $stream = @imap_open($mailbox, (string) $this->settings->imapUsername(), (string) $this->settings->imapPassword());

        if (! $stream) {
            throw new RuntimeException('IMAP connection failed: ' . implode('; ', imap_errors() ?: []));
        }

        $criteria = $this->settings->imapSearch();
        $messageNumbers = imap_search($stream, $criteria) ?: [];
        $matched = 0;

        foreach ($messageNumbers as $messageNumber) {
            $overview = imap_fetch_overview($stream, (string) $messageNumber, 0)[0] ?? null;
            $references = trim((string) ($overview->references ?? ''));
            $inReplyTo = trim((string) ($overview->in_reply_to ?? ''));
            $needle = $this->extractOutboundMessageId($inReplyTo . ' ' . $references);

            if (! $needle) {
                continue;
            }

            $message = OutreachMessage::where('uuid', $needle)->first();

            if (! $message) {
                continue;
            }

            if ($campaign && $message->campaign_id !== $campaign->id) {
                continue;
            }

            $message->forceFill([
                'status' => 'replied',
                'replied_at' => now(),
                'reply_excerpt' => $this->excerpt(imap_body($stream, $messageNumber, FT_PEEK) ?: ''),
                'provider_headers' => array_filter([
                    'subject' => $overview->subject ?? null,
                    'from' => $overview->from ?? null,
                    'date' => $overview->date ?? null,
                    'message_id' => $overview->message_id ?? null,
                    'in_reply_to' => $overview->in_reply_to ?? null,
                    'references' => $overview->references ?? null,
                ]),
            ])->save();

            $matched++;
        }

        imap_close($stream);

        return [
            'searched' => count($messageNumbers),
            'matched' => $matched,
        ];
    }

    protected function syncWithSocketClient(?OutreachCampaign $campaign = null): array
    {
        $username = (string) $this->settings->imapUsername();
        $password = (string) $this->settings->imapPassword();

        if ($username === '' || $password === '') {
            throw new RuntimeException('IMAP username or password is missing.');
        }

        $client = new SimpleImapClient(
            (string) $this->settings->imapHost(),
            $this->settings->imapPort(),
            (string) $this->settings->imapEncryption()
        );

        $client->connect($username, $password, $this->settings->imapMailbox());

        try {
            $messageNumbers = $client->search($this->settings->imapSearch());
            $matched = 0;

            foreach ($messageNumbers as $messageNumber) {
                $overview = $client->fetchOverview($messageNumber);
                $references = trim((string) ($overview['references'] ?? ''));
                $inReplyTo = trim((string) ($overview['in_reply_to'] ?? ''));
                $needle = $this->extractOutboundMessageId($inReplyTo . ' ' . $references);

                if (! $needle) {
                    continue;
                }

                $message = OutreachMessage::where('uuid', $needle)->first();

                if (! $message) {
                    continue;
                }

                if ($campaign && $message->campaign_id !== $campaign->id) {
                    continue;
                }

                $message->forceFill([
                    'status' => 'replied',
                    'replied_at' => now(),
                    'reply_excerpt' => $this->excerpt($client->fetchBodyPreview($messageNumber)),
                    'provider_headers' => array_filter([
                        'subject' => $overview['subject'] ?? null,
                        'from' => $overview['from'] ?? null,
                        'date' => $overview['date'] ?? null,
                        'message_id' => $overview['message_id'] ?? null,
                        'in_reply_to' => $overview['in_reply_to'] ?? null,
                        'references' => $overview['references'] ?? null,
                        'source' => 'socket-imap',
                    ]),
                ])->save();

                $matched++;
            }

            return [
                'searched' => count($messageNumbers),
                'matched' => $matched,
            ];
        } finally {
            $client->disconnect();
        }
    }

    protected function mailboxString(): string
    {
        $host = (string) $this->settings->imapHost();
        $port = $this->settings->imapPort();
        $encryption = trim((string) $this->settings->imapEncryption());
        $mailbox = trim((string) $this->settings->imapMailbox());

        if ($host === '') {
            throw new RuntimeException('IMAP host is missing.');
        }

        return sprintf('{%s:%d/imap/%s}%s', $host, $port, $encryption, $mailbox);
    }

    protected function extractOutboundMessageId(string $headers): ?string
    {
        if (! preg_match('/outreach\.([A-Za-z0-9]+)@/i', $headers, $matches)) {
            return null;
        }

        return $matches[1] ?? null;
    }

    protected function excerpt(string $body): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', strip_tags($body)) ?: '');

        return mb_substr($clean, 0, 1000);
    }
}
