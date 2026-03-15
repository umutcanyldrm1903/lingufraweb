<?php

namespace App\Services\Outreach;

use App\Models\OutreachLead;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class LushaClient
{
    public function __construct(
        protected OutreachSettingResolver $settings
    ) {
    }

    public function searchContacts(array $payload): array
    {
        $response = $this->request(
            $this->settings->lushaSearchPath(),
            $payload
        );

        return [
            'request_id' => $this->extractString($response, ['requestId', 'request_id', 'data.requestId', 'data.request_id']),
            'contacts' => $this->normalizeContacts($response),
            'raw' => $response,
        ];
    }

    public function enrichLead(OutreachLead $lead): array
    {
        if (! $lead->request_id || ! $lead->contact_id) {
            throw new RuntimeException("Lead {$lead->id} has no request_id/contact_id pair for enrich.");
        }

        $response = $this->request(
            $this->settings->lushaEnrichPath(),
            [
                'requestId' => $lead->request_id,
                'contactsIds' => [$lead->contact_id],
            ]
        );

        $contacts = $this->normalizeContacts($response);

        return $contacts[0] ?? [];
    }

    protected function request(string $path, array $payload): array
    {
        $apiKey = (string) $this->settings->lushaApiKey();

        if ($apiKey === '') {
            throw new RuntimeException('LUSHA_API_KEY is missing.');
        }

        $headerKey = trim((string) $this->settings->lushaApiKeyPrefix());
        $headerValue = $headerKey !== '' ? $headerKey . ' ' . $apiKey : $apiKey;

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'api_key' => $headerValue,
        ];

        if ($this->settings->lushaSendAuthorizationHeader()) {
            $headers['Authorization'] = 'Bearer ' . $apiKey;
        }

        $response = Http::withHeaders($headers)
            ->retry(3, 1500)
            ->timeout($this->settings->lushaTimeout())
            ->baseUrl($this->settings->lushaBaseUrl())
            ->post(ltrim($path, '/'), $payload)
            ->throw()
            ->json();

        if (! is_array($response)) {
            throw new RuntimeException('Unexpected Lusha response format.');
        }

        return $response;
    }

    protected function normalizeContacts(array $response): array
    {
        $requestId = $this->extractString($response, ['requestId', 'request_id', 'data.requestId', 'data.request_id']);

        $contacts = data_get($response, 'contacts')
            ?? data_get($response, 'data.contacts')
            ?? data_get($response, 'data');

        if (is_array($contacts) && $this->isAssociative($contacts)) {
            $contacts = [$contacts];
        }

        $contacts = Arr::wrap($contacts);

        if ($contacts === [null] || $contacts === []) {
            $contacts = [$response];
        }

        return collect($contacts)
            ->filter(fn ($contact) => is_array($contact))
            ->map(function (array $contact) use ($requestId) {
                $email = $this->findFirstEmail($contact);
                $firstName = $this->extractString($contact, ['firstName', 'first_name']);
                $lastName = $this->extractString($contact, ['lastName', 'last_name']);
                $fullName = $this->extractString($contact, ['fullName', 'full_name', 'name']);

                if (! $fullName && ($firstName || $lastName)) {
                    $fullName = trim($firstName . ' ' . $lastName);
                }

                return [
                    'request_id' => $this->extractString($contact, ['requestId', 'request_id']) ?: $requestId,
                    'contact_id' => $this->extractString($contact, ['contactId', 'contact_id', 'id']),
                    'external_id' => $this->extractString($contact, ['externalId', 'external_id']),
                    'full_name' => $fullName,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'company_name' => $this->extractString($contact, ['companyName', 'company_name', 'company.name']),
                    'job_title' => $this->extractString($contact, ['jobTitle', 'job_title', 'title']),
                    'linkedin_url' => $this->extractString($contact, ['linkedinProfileUrl', 'linkedinUrl', 'linkedin_url']),
                    'location' => $this->extractString($contact, ['location', 'location.name', 'country']),
                    'source_metadata' => $contact,
                ];
            })
            ->filter(function (array $contact) {
                return $contact['contact_id'] || $contact['email'] || $contact['full_name'];
            })
            ->values()
            ->all();
    }

    protected function extractString(array $payload, array $paths): ?string
    {
        foreach ($paths as $path) {
            $value = data_get($payload, $path);

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }

    protected function findFirstEmail(array $payload): ?string
    {
        $candidate = $this->extractString($payload, [
            'email',
            'workEmail',
            'work_email',
            'businessEmail',
            'business_email',
            'companyEmail',
            'company_email',
            'emails.0.email',
            'emails.0.address',
            'emails.0',
        ]);

        if ($candidate && filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
            return strtolower($candidate);
        }

        return $this->walkForEmail($payload);
    }

    protected function walkForEmail(array $payload): ?string
    {
        foreach ($payload as $value) {
            if (is_array($value)) {
                $email = $this->walkForEmail($value);

                if ($email) {
                    return $email;
                }

                continue;
            }

            if (is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                return strtolower($value);
            }
        }

        return null;
    }

    protected function isAssociative(array $payload): bool
    {
        return array_keys($payload) !== range(0, count($payload) - 1);
    }
}
