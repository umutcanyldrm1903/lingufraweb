<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_onboardings')) {
            return;
        }

        Schema::table('user_onboardings', function (Blueprint $table) {
            if (!Schema::hasColumn('user_onboardings', 'referred_by_user_id')) {
                $table->foreignId('referred_by_user_id')
                    ->nullable()
                    ->after('referral_code')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        // Index is optional but helps lookups by code. Ignore if it already exists.
        try {
            DB::statement('CREATE INDEX user_onboardings_referral_code_index ON user_onboardings (referral_code)');
        } catch (\Throwable $e) {
            // no-op
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('user_onboardings')) {
            return;
        }

        Schema::table('user_onboardings', function (Blueprint $table) {
            if (Schema::hasColumn('user_onboardings', 'referred_by_user_id')) {
                $table->dropConstrainedForeignId('referred_by_user_id');
            }

            // Keep the index as it might be used elsewhere.
        });
    }
};
