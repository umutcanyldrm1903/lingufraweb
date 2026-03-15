<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outreach_messages', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('campaign_id')->constrained('outreach_campaigns')->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained('outreach_leads')->cascadeOnDelete();
            $table->string('status')->default('draft')->index();
            $table->string('ai_model')->nullable();
            $table->string('subject')->nullable();
            $table->longText('body_text')->nullable();
            $table->longText('body_html')->nullable();
            $table->json('preview_payload')->nullable();
            $table->json('risk_flags')->nullable();
            $table->string('prompt_version')->nullable();
            $table->text('generation_error')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->text('reply_excerpt')->nullable();
            $table->string('provider_message_id')->nullable()->index();
            $table->json('provider_headers')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('unsubscribe_token')->unique();
            $table->timestamps();

            $table->index(['campaign_id', 'lead_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outreach_messages');
    }
};
