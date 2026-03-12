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
        Schema::table('student_plans', function (Blueprint $table) {
            $table->unsignedInteger('lesson_duration')->default(40)->after('duration_months');
        });

        Schema::table('user_plans', function (Blueprint $table) {
            $table->unsignedInteger('lesson_duration')->default(40)->after('plan_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_plans', function (Blueprint $table) {
            $table->dropColumn('lesson_duration');
        });

        Schema::table('user_plans', function (Blueprint $table) {
            $table->dropColumn('lesson_duration');
        });
    }
};
