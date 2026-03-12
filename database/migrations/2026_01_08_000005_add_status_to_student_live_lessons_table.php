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
        Schema::table('student_live_lessons', function (Blueprint $table) {
            $table->string('status')->default('scheduled')->after('type');
            $table->string('cancelled_by')->nullable()->after('status');
            $table->string('cancelled_reason')->nullable()->after('cancelled_by');
            $table->dateTime('cancelled_at')->nullable()->after('cancelled_reason');
            $table->dateTime('ended_at')->nullable()->after('cancelled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_live_lessons', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'cancelled_by',
                'cancelled_reason',
                'cancelled_at',
                'ended_at',
            ]);
        });
    }
};
