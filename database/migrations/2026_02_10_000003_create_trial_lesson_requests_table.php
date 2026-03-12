<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('trial_lesson_requests')) {
            return;
        }

        Schema::create('trial_lesson_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('phone')->nullable();
            $table->string('status')->default('pending'); // pending|approved|rejected

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trial_lesson_requests');
    }
};

