<?php

namespace App\Console\Commands;

use App\Models\OutreachCampaign;
use Illuminate\Console\Command;

class OutreachCreateCampaign extends Command
{
    protected $signature = 'outreach:create-campaign
        {name : Campaign name}
        {--company-name=}
        {--company-website=}
        {--product-name=}
        {--language=}
        {--offer-summary=}
        {--audience-summary=}
        {--tone=consultative}
        {--daily-limit=}
        {--hourly-limit=}
        {--min-delay=}
        {--send-start=}
        {--send-end=}
        {--timezone=}
        {--approval=1}';

    protected $description = 'Create an outreach campaign.';

    public function handle(): int
    {
        $campaign = OutreachCampaign::create([
            'name' => $this->argument('name'),
            'status' => 'draft',
            'company_name' => $this->option('company-name'),
            'company_website' => $this->option('company-website'),
            'product_name' => $this->option('product-name'),
            'language' => $this->option('language') ?: config('outreach.defaults.language', 'tr'),
            'offer_summary' => $this->option('offer-summary'),
            'audience_summary' => $this->option('audience-summary'),
            'tone' => $this->option('tone') ?: 'consultative',
            'timezone' => $this->option('timezone') ?: config('outreach.defaults.timezone', 'UTC'),
            'daily_send_limit' => (int) ($this->option('daily-limit') ?: config('outreach.defaults.daily_send_limit', 40)),
            'hourly_send_limit' => (int) ($this->option('hourly-limit') ?: config('outreach.defaults.hourly_send_limit', 10)),
            'min_delay_seconds' => (int) ($this->option('min-delay') ?: config('outreach.defaults.min_delay_seconds', 180)),
            'send_start_hour' => (int) ($this->option('send-start') ?: config('outreach.defaults.send_start_hour', 9)),
            'send_end_hour' => (int) ($this->option('send-end') ?: config('outreach.defaults.send_end_hour', 18)),
            'require_approval' => (bool) $this->option('approval'),
        ]);

        $this->info("Campaign #{$campaign->id} created.");

        return self::SUCCESS;
    }
}
