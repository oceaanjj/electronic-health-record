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
        Schema::table('act_of_daily_living', function (Blueprint $table) {
            $table->text('mobility_alert')->nullable()->change();
            $table->text('hygiene_alert')->nullable()->change();
            $table->text('toileting_alert')->nullable()->change();
            $table->text('feeding_alert')->nullable()->change();
            $table->text('hydration_alert')->nullable()->change();
            $table->text('sleep_pattern_alert')->nullable()->change();
            $table->text('pain_level_alert')->nullable()->change();
        });

        Schema::table('physical_exams', function (Blueprint $table) {
            $table->text('general_appearance_alert')->nullable()->change();
            $table->text('skin_alert')->nullable()->change();
            $table->text('eye_alert')->nullable()->change();
            $table->text('oral_alert')->nullable()->change();
            $table->text('cardiovascular_alert')->nullable()->change();
            $table->text('abdomen_alert')->nullable()->change();
            $table->text('extremities_alert')->nullable()->change();
            $table->text('neurological_alert')->nullable()->change();
        });

        Schema::table('lab_values', function (Blueprint $table) {
            $table->text('wbc_alert')->nullable()->change();
            $table->text('rbc_alert')->nullable()->change();
            $table->text('hgb_alert')->nullable()->change();
            $table->text('hct_alert')->nullable()->change();
            $table->text('platelets_alert')->nullable()->change();
            $table->text('mcv_alert')->nullable()->change();
            $table->text('mch_alert')->nullable()->change();
            $table->text('mchc_alert')->nullable()->change();
            $table->text('rdw_alert')->nullable()->change();
            $table->text('neutrophils_alert')->nullable()->change();
            $table->text('lymphocytes_alert')->nullable()->change();
            $table->text('monocytes_alert')->nullable()->change();
            $table->text('eosinophils_alert')->nullable()->change();
            $table->text('basophils_alert')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not implemented back to string to avoid data loss if truncation happens on down
    }
};
