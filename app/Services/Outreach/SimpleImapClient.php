<?php

namespace App\Services\Outreach;

use RuntimeException;

class SimpleImapClient
{
    protected $stream = null;

    protected int $tagCounter = 1;

    public function __construct(
        protected string $host,
        protected int $port,
        protected string $encryption = 'ssl',
        protected int $timeout = 30
    ) {
    }

    public function connect(string $username, string $password, string $mailbox): void
    {
        $this->openSocket();

        $greeting = $this->readLine();

        if (! preg_match('/^\*\s+(OK|PREAUTH)\b/i', $greeting)) {
            throw new RuntimeException('Unexpected IMAP greeting: ' . trim($greeting));
        }

        if ($this->transportMode() === 'tls') {
            $this->command('STARTTLS');

            if (! stream_socket_enable_crypto($this->stream, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException('Unable to negotiate STARTTLS for IMAP.');
            }
        }

        $this->command('LOGIN ' . $this->quote($username) . ' ' . $this->quote($password));
        $this->command('SELECT ' . $this->quote($mailbox));
    }

    public function search(string $criteria): array
    {
        $response = $this->command('SEARCH ' . trim($criteria ?: 'ALL'));

        foreach ($response['lines'] as $line) {
            if (! str_starts_with($line, '* SEARCH')) {
                continue;
            }

            $ids = preg_split('/\s+/', trim(substr($line, 8))) ?: [];

            return collect($ids)
                ->filter(fn ($id) => ctype_digit($id))
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        return [];
    }

    public function fetchOverview(int $messageNumber): array
    {
        $response = $this->command(sprintf(
            'FETCH %d (BODY.PEEK[HEADER.FIELDS (SUBJECT FROM DATE MESSAGE-ID IN-REPLY-TO REFERENCES)])',
            $messageNumber
        ));

        return $this->parseHeaders($response['literals'][0] ?? '');
    }

    public function fetchBodyPreview(int $messageNumber, int $length = 4096): string
    {
        $response = $this->command(sprintf(
            'FETCH %d (BODY.PEEK[TEXT]<0.%d>)',
            $messageNumber,
            max(256, $length)
        ));

        return trim((string) ($response['literals'][0] ?? ''));
    }

    public function disconnect(): void
    {
        if (! is_resource($this->stream)) {
            return;
        }

        try {
            $this->command('LOGOUT');
        } catch (\Throwable) {
        }

        fclose($this->stream);
        $this->stream = null;
    }

    protected function openSocket(): void
    {
        $transport = $this->transportMode() === 'ssl' ? 'ssl' : 'tcp';
        $endpoint = sprintf('%s://%s:%d', $transport, $this->host, $this->port);

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => ! $this->skipCertificateValidation(),
                'verify_peer_name' => ! $this->skipCertificateValidation(),
                'allow_self_signed' => $this->skipCertificateValidation(),
            ],
        ]);

        $stream = @stream_socket_client(
            $endpoint,
            $errorNumber,
            $errorMessage,
            $this->timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (! is_resource($stream)) {
            throw new RuntimeException(sprintf('IMAP connection failed: %s (%s)', $errorMessage ?: 'unknown error', $errorNumber ?: 'n/a'));
        }

        stream_set_timeout($stream, $this->timeout);

        $this->stream = $stream;
    }

    protected function command(string $command): array
    {
        if (! is_resource($this->stream)) {
            throw new RuntimeException('IMAP connection is not open.');
        }

        $tag = sprintf('A%04d', $this->tagCounter++);
        $written = fwrite($this->stream, $tag . ' ' . $command . "\r\n");

        if ($written === false) {
            throw new RuntimeException('Failed to write IMAP command.');
        }

        $lines = [];
        $literals = [];

        while (! feof($this->stream)) {
            $line = $this->readLine();
            $trimmedLine = rtrim($line, "\r\n");

            if ($trimmedLine !== '') {
                $lines[] = $trimmedLine;
            }

            if (preg_match('/\{(\d+)\}$/', $trimmedLine, $matches)) {
                $literal = $this->readBytes((int) $matches[1]);
                $literals[] = $literal;
            }

            if (preg_match('/^' . preg_quote($tag, '/') . '\s+(OK|NO|BAD)\b(.*)$/i', $trimmedLine, $matches)) {
                $status = strtoupper($matches[1]);

                if ($status !== 'OK') {
                    throw new RuntimeException(trim($matches[2]) ?: ('IMAP command failed: ' . $command));
                }

                return [
                    'lines' => $lines,
                    'literals' => $literals,
                ];
            }
        }

        throw new RuntimeException('IMAP connection closed unexpectedly.');
    }

    protected function parseHeaders(string $headers): array
    {
        $normalized = preg_replace("/\r?\n[ \t]+/", ' ', trim($headers)) ?: trim($headers);
        $result = [];

        foreach (preg_split("/\r\n|\n|\r/", $normalized) ?: [] as $line) {
            if (! str_contains($line, ':')) {
                continue;
            }

            [$name, $value] = array_map('trim', explode(':', $line, 2));
            $key = strtolower($name);
            $decoded = $this->decodeHeaderValue($value);

            $result[$key] = $decoded;
        }

        return [
            'subject' => $result['subject'] ?? null,
            'from' => $result['from'] ?? null,
            'date' => $result['date'] ?? null,
            'message_id' => $result['message-id'] ?? null,
            'in_reply_to' => $result['in-reply-to'] ?? null,
            'references' => $result['references'] ?? null,
        ];
    }

    protected function decodeHeaderValue(string $value): string
    {
        $decoded = @iconv_mime_decode($value, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, 'UTF-8');

        return trim($decoded !== false ? $decoded : $value);
    }

    protected function readLine(): string
    {
        $line = fgets($this->stream);

        if ($line === false) {
            $meta = stream_get_meta_data($this->stream);

            if (! empty($meta['timed_out'])) {
                throw new RuntimeException('Timed out while reading IMAP response.');
            }

            throw new RuntimeException('Failed to read IMAP response.');
        }

        return $line;
    }

    protected function readBytes(int $length): string
    {
        $buffer = '';

        while (strlen($buffer) < $length && ! feof($this->stream)) {
            $chunk = fread($this->stream, $length - strlen($buffer));

            if ($chunk === false) {
                throw new RuntimeException('Failed to read IMAP literal response.');
            }

            $buffer .= $chunk;
        }

        if (strlen($buffer) !== $length) {
            throw new RuntimeException('IMAP literal response was truncated.');
        }

        return $buffer;
    }

    protected function transportMode(): string
    {
        $encryption = strtolower(trim($this->encryption));

        if (str_contains($encryption, 'tls')) {
            return 'tls';
        }

        if (str_contains($encryption, 'ssl')) {
            return 'ssl';
        }

        return 'none';
    }

    protected function skipCertificateValidation(): bool
    {
        return str_contains(strtolower($this->encryption), 'novalidate-cert');
    }

    protected function quote(string $value): string
    {
        return '"' . addcslashes($value, '\\"') . '"';
    }
}
