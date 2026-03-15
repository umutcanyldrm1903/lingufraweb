<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outreach_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('outreach_campaigns')->cascadeOnDelete();
            $table->string('status')->default('imported');
            $table->string('source')->default('lusha');
            $table->string('contact_id')->nullable()->index();
            $table->string('request_id')->nullable()->index();
            $table->string('external_id')->nullable()->index();
            $table->string('full_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('company_name')->nullable();
            $table->string('job_title')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('location')->nullable();
            $table->json('source_metadata')->nullable();
            $table->json('enrichment_payload')->nullable();
            $table->timestamp('last_enriched_at')->nullable();
            $table->timestamp('opted_out_at')->nullable();
            $table->timestamp('invalid_email_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outreach_leads');
    }
};
