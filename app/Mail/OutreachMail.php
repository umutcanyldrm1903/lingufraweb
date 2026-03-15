<?php

namespace App\Mail;

use App\Models\OutreachMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class OutreachMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public OutreachMessage $outreachMessage)
    {
        $this->outreachMessage->loadMissing('campaign', 'lead');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (string) $this->outreachMessage->subject
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.outreach',
            text: 'emails.outreach-text',
            with: [
                'outreachMessage' => $this->outreachMessage,
            ]
        );
    }

    public function headers(): Headers
    {
        $headers = [
            'X-Auto-Response-Suppress' => 'All',
        ];

        $listUnsubscribe = $this->listUnsubscribeValue();
        if ($listUnsubscribe) {
            $headers['List-Unsubscribe'] = $listUnsubscribe;
        }

        return new Headers(
            messageId: $this->outreachMessage->outboundMessageId(),
            text: $headers,
        );
    }

    public function attachments(): array
    {
        return [];
    }

    protected function listUnsubscribeValue(): ?string
    {
        $mailto = trim((string) ($this->outreachMessage->campaign->unsubscribe_mailto ?: config('mail.from.address')));
        $url = $this->outreachMessage->unsubscribeUrl();

        $parts = [];

        if ($mailto !== '') {
            $parts[] = '<mailto:' . $mailto . '>';
        }

        if ($url) {
            $parts[] = '<' . $url . '>';
        }

        return $parts !== [] ? implode(', ', $parts) : null;
    }
}
