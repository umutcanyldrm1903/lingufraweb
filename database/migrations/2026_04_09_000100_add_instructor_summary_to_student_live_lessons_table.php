<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_live_lessons', function (Blueprint $table) {
            if (!Schema::hasColumn('student_live_lessons', 'instructor_summary')) {
                $table->text('instructor_summary')->nullable()->after('ended_at');
            }

            if (!Schema::hasColumn('student_live_lessons', 'instructor_summary_written_at')) {
                $table->timestamp('instructor_summary_written_at')->nullable()->after('instructor_summary');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_live_lessons', function (Blueprint $table) {
            if (Schema::hasColumn('student_live_lessons', 'instructor_summary_written_at')) {
                $table->dropColumn('instructor_summary_written_at');
            }

            if (Schema::hasColumn('student_live_lessons', 'instructor_summary')) {
                $table->dropColumn('instructor_summary');
            }
        });
    }
};
