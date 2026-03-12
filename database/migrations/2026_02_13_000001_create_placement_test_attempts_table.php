<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('placement_test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source', 20)->default('web')->index();
            $table->string('locale', 10)->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable()->index();
            $table->string('contact_phone', 32)->nullable();
            $table->unsignedSmallInteger('score')->default(0);
            $table->unsignedSmallInteger('max_score')->default(0);
            $table->unsignedTinyInteger('answered_count')->default(0);
            $table->string('level', 8)->index();
            $table->string('recommended_track', 80)->nullable();
            $table->json('answers');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placement_test_attempts');
    }
};

