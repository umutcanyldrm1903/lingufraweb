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
        Schema::create('student_homework_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_homework_id')->constrained('student_homeworks')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('submission_path')->nullable();
            $table->string('submission_name')->nullable();
            $table->text('note')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->string('status')->default('submitted');
            $table->timestamps();

            $table->unique(['student_homework_id', 'student_id'], 'student_homework_submission_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_homework_submissions');
    }
};
