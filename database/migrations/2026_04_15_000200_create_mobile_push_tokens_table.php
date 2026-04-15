<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_push_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('token');
            $table->string('platform', 20)->default('unknown');
            $table->string('timezone', 80)->default('Europe/Istanbul');
            $table->string('locale', 10)->nullable();
            $table->string('reminder_window', 20)->default('evening');
            $table->boolean('reminders_enabled')->default(true);
            $table->date('last_daily_sent_on')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'token'], 'mobile_push_tokens_user_token_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_push_tokens');
    }
};
