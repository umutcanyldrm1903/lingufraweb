<?php

namespace App\Services\Outreach;

use Illuminate\Support\Facades\Cache;

class OutreachSettingResolver
{
    public function openAiApiKey(): ?string
    {
        return $this->string('outreach_openai_api_key', config('outreach.openai.api_key'));
    }

    public function openAiBaseUrl(): string
    {
        return $this->string('outreach_openai_base_url', config('outreach.openai.base_url', 'https://api.openai.com/v1'))
            ?: 'https://api.openai.com/v1';
    }

    public function openAiModel(): string
    {
        return $this->string('outreach_openai_model', config('outreach.openai.model', 'gpt-5-mini'))
            ?: 'gpt-5-mini';
    }

    public function openAiTimeout(): int
    {
        return $this->integer('outreach_openai_timeout', config('outreach.openai.timeout', 60), 60);
    }

    public function lushaApiKey(): ?string
    {
        return $this->string('outreach_lusha_api_key', config('outreach.lusha.api_key'));
    }

    public function lushaBaseUrl(): string
    {
        return $this->string('outreach_lusha_base_url', config('outreach.lusha.base_url', 'https://api.lusha.com'))
            ?: 'https://api.lusha.com';
    }

    public function lushaTimeout(): int
    {
        return $this->integer('outreach_lusha_timeout', config('outreach.lusha.timeout', 45), 45);
    }

    public function lushaSearchPath(): string
    {
        return $this->string('outreach_lusha_search_path', config('outreach.lusha.search_path', '/prospecting/contact/search'))
            ?: '/prospecting/contact/search';
    }

    public function lushaEnrichPath(): string
    {
        return $this->string('outreach_lusha_enrich_path', config('outreach.lusha.enrich_path', '/prospecting/contact/enrich'))
            ?: '/prospecting/contact/enrich';
    }

    public function lushaApiKeyPrefix(): string
    {
        return $this->string('outreach_lusha_api_key_prefix', config('outreach.lusha.api_key_prefix', '')) ?: '';
    }

    public function lushaSendAuthorizationHeader(): bool
    {
        return $this->boolean('outreach_lusha_send_authorization_header', config('outreach.lusha.send_authorization_header', false));
    }

    public function imapHost(): ?string
    {
        return $this->string('outreach_imap_host', config('outreach.imap.host'));
    }

    public function imapPort(): int
    {
        return $this->integer('outreach_imap_port', config('outreach.imap.port', 993), 993);
    }

    public function imapEncryption(): string
    {
        return $this->string('outreach_imap_encryption', config('outreach.imap.encryption', 'ssl')) ?: 'ssl';
    }

    public function imapUsername(): ?string
    {
        return $this->string('outreach_imap_username', config('outreach.imap.username'));
    }

    public function imapPassword(): ?string
    {
        return $this->string('outreach_imap_password', config('outreach.imap.password'));
    }

    public function imapMailbox(): string
    {
        return $this->string('outreach_imap_mailbox', config('outreach.imap.mailbox', 'INBOX')) ?: 'INBOX';
    }

    public function imapSearch(): string
    {
        return $this->string('outreach_imap_search', config('outreach.imap.search', 'UNSEEN')) ?: 'UNSEEN';
    }

    protected function string(string $key, mixed $fallback = null): ?string
    {
        $setting = Cache::get('setting');
        $value = $setting?->{$key};

        if (is_string($value) && trim($value) !== '') {
            return trim($value);
        }

        if (is_string($fallback) && trim($fallback) !== '') {
            return trim($fallback);
        }

        return null;
    }

    protected function integer(string $key, mixed $fallback = null, int $default = 0): int
    {
        $setting = Cache::get('setting');
        $value = $setting?->{$key};

        if (is_numeric($value)) {
            return (int) $value;
        }

        if (is_numeric($fallback)) {
            return (int) $fallback;
        }

        return $default;
    }

    protected function boolean(string $key, mixed $fallback = null): bool
    {
        $setting = Cache::get('setting');
        $value = $setting?->{$key};

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'true', 'yes', 'active', 'on'], true);
        }

        if (is_bool($fallback)) {
            return $fallback;
        }

        if (is_string($fallback)) {
            return in_array(strtolower($fallback), ['1', 'true', 'yes', 'active', 'on'], true);
        }

        return (bool) $fallback;
    }
}
