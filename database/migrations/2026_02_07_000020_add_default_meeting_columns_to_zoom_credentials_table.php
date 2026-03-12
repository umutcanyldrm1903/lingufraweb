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
        Schema::table('zoom_credentials', function (Blueprint $table) {
            $table->string('default_meeting_id')->nullable()->after('scope');
            $table->string('default_meeting_password')->nullable()->after('default_meeting_id');
            $table->text('default_join_url')->nullable()->after('default_meeting_password');
            $table->dateTime('default_meeting_created_at')->nullable()->after('default_join_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_credentials', function (Blueprint $table) {
            $table->dropColumn([
                'default_meeting_id',
                'default_meeting_password',
                'default_join_url',
                'default_meeting_created_at',
            ]);
        });
    }
};

