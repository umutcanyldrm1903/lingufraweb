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
            $table->text('access_token')->nullable()->after('client_secret');
            $table->text('refresh_token')->nullable()->after('access_token');
            $table->dateTime('token_expires_at')->nullable()->after('refresh_token');
            $table->string('zoom_user_id')->nullable()->after('token_expires_at');
            $table->string('zoom_email')->nullable()->after('zoom_user_id');
            $table->text('scope')->nullable()->after('zoom_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_credentials', function (Blueprint $table) {
            $table->dropColumn([
                'access_token',
                'refresh_token',
                'token_expires_at',
                'zoom_user_id',
                'zoom_email',
                'scope',
            ]);
        });
    }
};
