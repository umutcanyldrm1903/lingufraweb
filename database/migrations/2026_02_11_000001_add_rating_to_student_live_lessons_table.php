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
        if (!Schema::hasTable('student_live_lessons')) {
            return;
        }

        Schema::table('student_live_lessons', function (Blueprint $table) {
            if (!Schema::hasColumn('student_live_lessons', 'student_rating')) {
                $table->unsignedTinyInteger('student_rating')->nullable()->after('ended_at');
            }

            if (!Schema::hasColumn('student_live_lessons', 'student_review')) {
                $table->text('student_review')->nullable()->after('student_rating');
            }

            if (!Schema::hasColumn('student_live_lessons', 'rated_at')) {
                $table->timestamp('rated_at')->nullable()->after('student_review');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('student_live_lessons')) {
            return;
        }

        Schema::table('student_live_lessons', function (Blueprint $table) {
            $drop = [];

            foreach (['rated_at', 'student_review', 'student_rating'] as $col) {
                if (Schema::hasColumn('student_live_lessons', $col)) {
                    $drop[] = $col;
                }
            }

            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};

