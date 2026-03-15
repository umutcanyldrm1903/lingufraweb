<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outreach_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('name');
            $table->string('status')->default('draft');
            $table->string('company_name')->nullable();
            $table->string('company_website')->nullable();
            $table->string('product_name')->nullable();
            $table->string('language', 10)->default(config('outreach.defaults.language', 'tr'));
            $table->text('audience_summary')->nullable();
            $table->text('offer_summary')->nullable();
            $table->string('tone')->default('consultative');
            $table->text('prompt_preamble')->nullable();
            $table->text('signature_text')->nullable();
            $table->longText('signature_html')->nullable();
            $table->string('unsubscribe_mailto')->nullable();
            $table->string('timezone')->default(config('outreach.defaults.timezone', config('app.timezone', 'UTC')));
            $table->unsignedInteger('daily_send_limit')->default((int) config('outreach.defaults.daily_send_limit', 40));
            $table->unsignedInteger('hourly_send_limit')->default((int) config('outreach.defaults.hourly_send_limit', 10));
            $table->unsignedInteger('min_delay_seconds')->default((int) config('outreach.defaults.min_delay_seconds', 180));
            $table->unsignedTinyInteger('send_start_hour')->default((int) config('outreach.defaults.send_start_hour', 9));
            $table->unsignedTinyInteger('send_end_hour')->default((int) config('outreach.defaults.send_end_hour', 18));
            $table->boolean('require_approval')->default((bool) config('outreach.defaults.require_approval', true));
            $table->json('last_lusha_payload')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outreach_campaigns');
    }
};
