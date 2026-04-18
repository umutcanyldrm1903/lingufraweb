<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source', 32)->default('mobile')->index();
            $table->string('name', 120)->index();
            $table->timestamp('event_time')->nullable()->index();
            $table->string('segment', 40)->nullable()->index();
            $table->string('experiment', 60)->nullable()->index();
            $table->ipAddress('ip')->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_analytics_events');
    }
};
