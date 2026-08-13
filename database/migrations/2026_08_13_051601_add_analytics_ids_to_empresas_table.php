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
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('analytics_ga4_id')->nullable()->after('sobre_texto');
            $table->string('analytics_gtm_id')->nullable()->after('analytics_ga4_id');
            $table->string('analytics_meta_pixel_id')->nullable()->after('analytics_gtm_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['analytics_ga4_id', 'analytics_gtm_id', 'analytics_meta_pixel_id']);
        });
    }
};
