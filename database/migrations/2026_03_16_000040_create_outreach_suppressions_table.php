<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outreach_suppressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('outreach_campaigns')->nullOnDelete();
            $table->string('email')->unique();
            $table->string('reason');
            $table->string('source')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('suppressed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outreach_suppressions');
    }
};
