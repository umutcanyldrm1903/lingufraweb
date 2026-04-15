<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_live_lessons', function (Blueprint $table) {
            if (!Schema::hasColumn('student_live_lessons', 'push_reminder_sent_at')) {
                $table->timestamp('push_reminder_sent_at')->nullable()->after('cancelled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_live_lessons', function (Blueprint $table) {
            if (Schema::hasColumn('student_live_lessons', 'push_reminder_sent_at')) {
                $table->dropColumn('push_reminder_sent_at');
            }
        });
    }
};
