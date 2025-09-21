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
        Schema::create('lab_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->date('record_date');

            // Lab test results and normal ranges
            $table->string('wbc_result')->nullable();
            $table->string('wbc_normal_range')->nullable();
            $table->string('rbc_result')->nullable();
            $table->string('rbc_normal_range')->nullable();
            $table->string('hgb_result')->nullable();
            $table->string('hgb_normal_range')->nullable();
            $table->string('hct_result')->nullable();
            $table->string('hct_normal_range')->nullable();
            $table->string('platelets_result')->nullable();
            $table->string('platelets_normal_range')->nullable();
            $table->string('mcv_result')->nullable();
            $table->string('mcv_normal_range')->nullable();
            $table->string('mch_result')->nullable();
            $table->string('mch_normal_range')->nullable();
            $table->string('mchc_result')->nullable();
            $table->string('mchc_normal_range')->nullable();
            $table->string('rdw_result')->nullable();
            $table->string('rdw_normal_range')->nullable();
            $table->string('neutrophils_result')->nullable();
            $table->string('neutrophils_normal_range')->nullable();
            $table->string('lymphocytes_result')->nullable();
            $table->string('lymphocytes_normal_range')->nullable();
            $table->string('monocytes_result')->nullable();
            $table->string('monocytes_normal_range')->nullable();
            $table->string('eosinophils_result')->nullable();
            $table->string('eosinophils_normal_range')->nullable();
            $table->string('basophils_result')->nullable();
            $table->string('basophils_normal_range')->nullable();

            $table->timestamps();

            // Ensure a patient can only have one lab record for a specific date
            $table->unique(['patient_id', 'record_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_values');
    }
};
