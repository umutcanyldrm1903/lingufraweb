<?php

namespace App\Services\Outreach;

use App\Models\OutreachCampaign;
use App\Models\OutreachLead;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OutreachAiComposer
{
    public const PROMPT_VERSION = '2026-03-16.v1';

    public function __construct(
        protected OutreachSettingResolver $settings
    ) {
    }

    public function compose(OutreachCampaign $campaign, OutreachLead $lead): array
    {
        try {
            $apiKey = (string) $this->settings->openAiApiKey();

            if ($apiKey === '') {
                throw new RuntimeException('OPENAI_API_KEY is missing.');
            }

            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout($this->settings->openAiTimeout())
                ->baseUrl($this->settings->openAiBaseUrl())
                ->post('responses', [
                    'model' => $this->settings->openAiModel(),
                    'input' => $this->buildPrompt($campaign, $lead),
                    'max_output_tokens' => 900,
                ])
                ->throw()
                ->json();

            $outputText = $response['output_text'] ?? null;

            if (! is_string($outputText) || trim($outputText) === '') {
                $outputText = $this->extractOutputTextFromItems($response);
            }

            if (! $outputText) {
                throw new RuntimeException('OpenAI response had no output_text.');
            }

            return $this->normalizePayload(
                $this->decodeJsonPayload($outputText),
                $this->settings->openAiModel()
            );
        } catch (\Throwable $throwable) {
            $fallback = $this->fallbackMessage($campaign, $lead);
            $fallback['generation_error'] = $throwable->getMessage();

            return $fallback;
        }
    }

    protected function buildPrompt(OutreachCampaign $campaign, OutreachLead $lead): string
    {
        $language = $this->languageLabel($campaign->language);

        return implode("\n", array_filter([
            'You write concise B2B outreach emails.',
            'Return valid JSON only. No markdown, no code fences.',
            'Language: ' . $language . '.',
            'Tone: ' . ($campaign->tone ?: 'consultative') . '.',
            'Rules:',
            '- Max 120 words total.',
            '- Do not invent facts about the person or company.',
            '- Avoid spammy phrases, hype, urgency, fake familiarity, and excessive punctuation.',
            '- Keep the subject line under 8 words.',
            '- Use one clear CTA.',
            '- If context is thin, stay neutral and honest.',
            '- risk_flags must only contain values from: missing_name, missing_company, no_offer, uncertain_fit, generic_copy.',
            '',
            'Company context:',
            'company_name: ' . ($campaign->company_name ?: 'unknown'),
            'company_website: ' . ($campaign->company_website ?: 'unknown'),
            'product_name: ' . ($campaign->product_name ?: 'unknown'),
            'offer_summary: ' . ($campaign->offer_summary ?: 'unknown'),
            'audience_summary: ' . ($campaign->audience_summary ?: 'unknown'),
            'extra_context: ' . ($campaign->prompt_preamble ?: 'none'),
            '',
            'Lead context:',
            'full_name: ' . ($lead->full_name ?: 'unknown'),
            'first_name: ' . ($lead->first_name ?: 'unknown'),
            'company_name: ' . ($lead->company_name ?: 'unknown'),
            'job_title: ' . ($lead->job_title ?: 'unknown'),
            'location: ' . ($lead->location ?: 'unknown'),
            '',
            'Return JSON with this exact shape:',
            '{"subject":"", "opening_line":"", "body_lines":["",""], "cta":"", "risk_flags":[""]}',
        ]));
    }

    protected function normalizePayload(array $payload, string $model): array
    {
        $bodyLines = collect($payload['body_lines'] ?? [])
            ->filter(fn ($line) => is_string($line) && trim($line) !== '')
            ->map(fn ($line) => trim($line))
            ->take(3)
            ->values()
            ->all();

        if ($bodyLines === []) {
            $bodyLines = ['Kisa bir tanisma notu birakmak istedim.'];
        }

        $riskFlags = collect($payload['risk_flags'] ?? [])
            ->filter(fn ($flag) => is_string($flag) && trim($flag) !== '')
            ->map(fn ($flag) => trim($flag))
            ->unique()
            ->values()
            ->all();

        return [
            'subject' => trim((string) ($payload['subject'] ?? 'Kisa bir tanisma notu')),
            'opening_line' => trim((string) ($payload['opening_line'] ?? 'Merhaba,')),
            'body_lines' => $bodyLines,
            'cta' => trim((string) ($payload['cta'] ?? 'Uygunsa kisa bir gorusme planlayabiliriz.')),
            'risk_flags' => $riskFlags,
            'ai_model' => $model,
            'generation_error' => null,
        ];
    }

    protected function decodeJsonPayload(string $outputText): array
    {
        $trimmed = trim($outputText);
        $trimmed = preg_replace('/^```(?:json)?\s*/i', '', $trimmed) ?: $trimmed;
        $trimmed = preg_replace('/\s*```$/', '', $trimmed) ?: $trimmed;

        $decoded = json_decode($trimmed, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $trimmed, $match)) {
            $decoded = json_decode($match[0], true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        throw new RuntimeException('Failed to decode JSON returned by OpenAI.');
    }

    protected function extractOutputTextFromItems(array $response): ?string
    {
        $output = $response['output'] ?? [];

        foreach ($output as $item) {
            foreach ($item['content'] ?? [] as $content) {
                $text = $content['text'] ?? null;

                if (is_string($text) && trim($text) !== '') {
                    return trim($text);
                }
            }
        }

        return null;
    }

    protected function fallbackMessage(OutreachCampaign $campaign, OutreachLead $lead): array
    {
        $isTurkish = str_starts_with(strtolower((string) $campaign->language), 'tr');
        $name = $lead->first_name ?: $lead->full_name ?: ($isTurkish ? 'Merhaba' : 'Hello');
        $company = $lead->company_name ?: $campaign->company_name ?: ($isTurkish ? 'ekibiniz' : 'your team');
        $offer = $campaign->offer_summary ?: ($isTurkish ? 'sizinle kisa bir tanisma yapmak istiyorum.' : 'I wanted to introduce what we do.');

        if ($isTurkish) {
            return [
                'subject' => 'Kisa bir tanisma',
                'opening_line' => "{$name}, merhaba.",
                'body_lines' => [
                    "{$company} tarafinda yaptiginiz calismalari baz alarak kisaca ulasmak istedim.",
                    $offer,
                ],
                'cta' => 'Uygunsaniz bu hafta 10 dakikalik bir gorusme planlayabiliriz.',
                'risk_flags' => collect([
                    $lead->first_name || $lead->full_name ? null : 'missing_name',
                    $lead->company_name ? null : 'missing_company',
                    $campaign->offer_summary ? null : 'no_offer',
                ])->filter()->values()->all(),
                'ai_model' => 'fallback-template',
                'generation_error' => null,
            ];
        }

        return [
            'subject' => 'Quick introduction',
            'opening_line' => "Hello {$name},",
            'body_lines' => [
                "I wanted to reach out with a short introduction tailored to {$company}.",
                $offer,
            ],
            'cta' => 'If it is relevant, we can schedule a short call this week.',
            'risk_flags' => collect([
                $lead->first_name || $lead->full_name ? null : 'missing_name',
                $lead->company_name ? null : 'missing_company',
                $campaign->offer_summary ? null : 'no_offer',
            ])->filter()->values()->all(),
            'ai_model' => 'fallback-template',
            'generation_error' => null,
        ];
    }

    protected function languageLabel(?string $language): string
    {
        return match (strtolower((string) $language)) {
            'tr', 'tr-tr' => 'Turkish',
            default => 'English',
        };
    }
}
