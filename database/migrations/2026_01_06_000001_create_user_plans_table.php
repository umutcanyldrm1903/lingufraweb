<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            $table->string('plan_key')->nullable();
            $table->string('plan_title')->nullable();
            $table->unsignedInteger('lessons_total')->default(0);
            $table->unsignedInteger('lessons_remaining')->default(0);
            $table->unsignedInteger('cancel_total')->default(0);
            $table->unsignedInteger('cancel_remaining')->default(0);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->foreignId('last_order_id')->nullable()->constrained('orders')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_plans');
    }
};

