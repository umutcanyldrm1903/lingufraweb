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
        Schema::create('student_library_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('category')->default('general');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();
            $table->timestamps();

            $table->index(['instructor_id', 'student_id']);
            $table->index(['student_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_library_items');
    }
};
