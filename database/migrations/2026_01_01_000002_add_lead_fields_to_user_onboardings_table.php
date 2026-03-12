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
        Schema::table('user_onboardings', function (Blueprint $table) {
            $table->string('lesson_place')->nullable();
            $table->string('student_type')->nullable();
            $table->string('goal')->nullable();
            $table->string('level')->nullable();
            $table->string('frequency')->nullable();
            $table->text('details')->nullable();
            $table->string('start_when')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_onboardings', function (Blueprint $table) {
            $table->dropColumn([
                'lesson_place',
                'student_type',
                'goal',
                'level',
                'frequency',
                'details',
                'start_when',
            ]);
        });
    }
};
