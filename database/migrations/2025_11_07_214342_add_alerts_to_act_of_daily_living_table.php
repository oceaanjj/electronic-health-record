<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('act_of_daily_living', function (Blueprint $table) {
            $table->string('mobility_alert')->nullable();
            $table->string('hygiene_alert')->nullable()->after('mobility_alert');
            $table->string('toileting_alert')->nullable()->after('hygiene_alert');
            $table->string('feeding_alert')->nullable()->after('toileting_alert');
            $table->string('hydration_alert')->nullable()->after('feeding_alert');
            $table->string('sleep_pattern_alert')->nullable()->after('hydration_alert');
            $table->string('pain_level_alert')->nullable()->after('sleep_pattern_alert');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('act_of_daily_living', function (Blueprint $table) {
            $table->dropColumn([
                'mobility_alert',
                'hygiene_alert',
                'toileting_alert',
                'feeding_alert',
                'hydration_alert',
                'sleep_pattern_alert',
                'pain_level_alert',
            ]);
        });
    }
};