<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_push_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('segment', 40)->default('all')->index();
            $table->string('title_tr', 120);
            $table->string('body_tr', 240);
            $table->string('title_en', 120);
            $table->string('body_en', 240);
            $table->string('deep_link', 80)->default('/start-speaking');
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_push_campaigns');
    }
};
