<?php

namespace App\Console\Commands;

use App\Models\OutreachCampaign;
use App\Services\Outreach\LushaClient;
use App\Services\Outreach\OutreachLeadManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;

class OutreachImportLusha extends Command
{
    protected $signature = 'outreach:import-lusha
        {campaignId : Outreach campaign id}
        {--payload= : Raw JSON payload for Lusha search}
        {--file= : Path to JSON file with the request payload}';

    protected $description = 'Import leads from Lusha contact search into a campaign.';

    public function handle(LushaClient $lushaClient, OutreachLeadManager $leadManager): int
    {
        $campaign = OutreachCampaign::findOrFail((int) $this->argument('campaignId'));
        $payload = $this->resolvePayload();
        $result = $lushaClient->searchContacts($payload);
        $count = 0;

        foreach ($result['contacts'] as $contact) {
            $leadManager->upsertFromLusha($campaign, $contact, 'imported');
            $count++;
        }

        $campaign->forceFill([
            'status' => 'imported',
            'last_lusha_payload' => $payload,
        ])->save();

        $this->info("Imported {$count} lead(s) into campaign #{$campaign->id}.");

        if (($result['request_id'] ?? null) !== null) {
            $this->line('Lusha request id: ' . $result['request_id']);
        }

        return self::SUCCESS;
    }

    protected function resolvePayload(): array
    {
        $payload = $this->option('payload');
        $file = $this->option('file');

        if ($payload) {
            return $this->decodePayload($payload);
        }

        if ($file) {
            if (! File::exists($file)) {
                throw new RuntimeException("Payload file not found: {$file}");
            }

            return $this->decodePayload((string) File::get($file));
        }

        throw new RuntimeException('Provide either --payload or --file.');
    }

    protected function decodePayload(string $payload): array
    {
        $decoded = json_decode($payload, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            throw new RuntimeException('Payload must be valid JSON.');
        }

        return $decoded;
    }
}
