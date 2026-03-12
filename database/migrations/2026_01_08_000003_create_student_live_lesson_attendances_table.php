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
        Schema::create('student_live_lesson_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_live_lesson_id')->constrained('student_live_lessons')->cascadeOnDelete();
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['student_id', 'student_live_lesson_id'], 'student_live_attendance_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_live_lesson_attendances');
    }
};
