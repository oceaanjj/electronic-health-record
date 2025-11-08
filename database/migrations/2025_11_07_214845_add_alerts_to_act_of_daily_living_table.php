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


            if (!Schema::hasColumn('act_of_daily_living', 'mobility_alert')) {
                $table->string('mobility_alert')->nullable()->after('patient_id');
            }
            if (!Schema::hasColumn('act_of_daily_living', 'hygiene_alert')) {
                $table->string('hygiene_alert')->nullable()->after('mobility_alert');
            }
            if (!Schema::hasColumn('act_of_daily_living', 'toileting_alert')) {
                $table->string('toileting_alert')->nullable()->after('hygiene_alert');
            }
            if (!Schema::hasColumn('act_of_daily_living', 'feeding_alert')) {
                $table->string('feeding_alert')->nullable()->after('toileting_alert');
            }
            if (!Schema::hasColumn('act_of_daily_living', 'hydration_alert')) {
                $table->string('hydration_alert')->nullable()->after('feeding_alert');
            }
            if (!Schema::hasColumn('act_of_daily_living', 'sleep_pattern_alert')) {
                $table->string('sleep_pattern_alert')->nullable()->after('hydration_alert');
            }
            if (!Schema::hasColumn('act_of_daily_living', 'pain_level_alert')) {
                $table->string('pain_level_alert')->nullable()->after('sleep_pattern_alert');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the column exists before dropping it to prevent errors on rollback.
        Schema::table('act_of_daily_living', function (Blueprint $table) {
            $columnsToDrop = [
                'mobility_alert',
                'hygiene_alert',
                'toileting_alert',
                'feeding_alert',
                'hydration_alert',
                'sleep_pattern_alert',
                'pain_level_alert',
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('act_of_daily_living', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};