<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('student_plans', function (Blueprint $table) {
            $table->id();

            $table->string('key')->unique();
            $table->string('title');
            $table->string('display_title')->nullable();

            $table->string('label')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('tagline')->nullable();

            $table->unsignedInteger('duration_months')->default(0);
            $table->unsignedInteger('lessons_total')->default(0);
            $table->unsignedInteger('cancel_total')->default(0);

            $table->unsignedDecimal('old_price', 10, 2)->default(0);
            $table->unsignedDecimal('price', 10, 2)->default(0);

            $table->boolean('featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });

        // Seed default plans from config (optional)
        $plans = (array) config('student_plans.plans', []);
        if (!empty($plans)) {
            $now = now();
            $rows = [];

            foreach ($plans as $plan) {
                $rows[] = [
                    'key' => (string) ($plan['key'] ?? ''),
                    'title' => (string) ($plan['title'] ?? ''),
                    'display_title' => (string) ($plan['display_title'] ?? '') ?: null,
                    'label' => (string) ($plan['label'] ?? '') ?: null,
                    'subtitle' => (string) ($plan['subtitle'] ?? '') ?: null,
                    'tagline' => (string) ($plan['tagline'] ?? '') ?: null,
                    'duration_months' => (int) ($plan['duration_months'] ?? 0),
                    'lessons_total' => (int) ($plan['lessons_total'] ?? 0),
                    'cancel_total' => (int) ($plan['cancel_total'] ?? 0),
                    'old_price' => (float) ($plan['old_price'] ?? 0),
                    'price' => (float) ($plan['price'] ?? 0),
                    'featured' => (bool) ($plan['featured'] ?? false),
                    'is_active' => true,
                    'sort_order' => (int) ($plan['sort_order'] ?? 0),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $rows = array_filter($rows, fn ($row) => !empty($row['key']));
            if (!empty($rows)) {
                DB::table('student_plans')->insert($rows);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_plans');
    }
};
