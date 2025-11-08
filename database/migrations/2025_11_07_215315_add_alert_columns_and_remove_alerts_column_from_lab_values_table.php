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
        Schema::table('lab_values', function (Blueprint $table) {
            $table->string('wbc_alert')->nullable();
            $table->string('rbc_alert')->nullable();
            $table->string('hgb_alert')->nullable();
            $table->string('hct_alert')->nullable();
            $table->string('platelets_alert')->nullable();
            $table->string('mcv_alert')->nullable();
            $table->string('mch_alert')->nullable();
            $table->string('mchc_alert')->nullable();
            $table->string('rdw_alert')->nullable();
            $table->string('neutrophils_alert')->nullable();
            $table->string('lymphocytes_alert')->nullable();
            $table->string('monocytes_alert')->nullable();
            $table->string('eosinophils_alert')->nullable();
            $table->string('basophils_alert')->nullable();
            $table->dropColumn('alerts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_values', function (Blueprint $table) {
            $table->dropColumn([
                'wbc_alert',
                'rbc_alert',
                'hgb_alert',
                'hct_alert',
                'platelets_alert',
                'mcv_alert',
                'mch_alert',
                'mchc_alert',
                'rdw_alert',
                'neutrophils_alert',
                'lymphocytes_alert',
                'monocytes_alert',
                'eosinophils_alert',
                'basophils_alert',
            ]);
            $table->string('alerts')->nullable();
        });
    }
};
