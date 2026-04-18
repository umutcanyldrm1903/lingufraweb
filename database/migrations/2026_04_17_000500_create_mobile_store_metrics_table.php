<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_store_metrics', function (Blueprint $table) {
            $table->id();
            $table->date('metric_date')->unique();
            $table->unsignedInteger('store_page_views')->default(0);
            $table->unsignedInteger('installs')->default(0);
            $table->unsignedInteger('trial_starts')->default(0);
            $table->unsignedInteger('trial_conversions')->default(0);
            $table->string('channel', 32)->default('organic')->index();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_store_metrics');
    }
};
