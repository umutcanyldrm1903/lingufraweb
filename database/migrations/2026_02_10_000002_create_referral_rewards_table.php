<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('referral_rewards')) {
            return;
        }

        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('referrer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('referred_user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            $table->unsignedInteger('reward_lessons')->default(0);

            $table->timestamps();

            $table->unique('referred_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_rewards');
    }
};

