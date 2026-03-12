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
        if (!Schema::hasColumn('users', 'instructor_profile')) {
            Schema::table('users', function (Blueprint $table) {
                $table->longText('instructor_profile')->nullable()->after('bio');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'instructor_profile')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('instructor_profile');
            });
        }
    }
};
