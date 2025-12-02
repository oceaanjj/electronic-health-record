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
        Schema::create('nursing_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id')->nullable();
            $table->foreignId('physical_exam_id')->nullable()->constrained('physical_exams')->onDelete('cascade');
            $table->foreignId('intake_and_output_id')->nullable()->constrained('intake_and_outputs')->onDelete('cascade');
            $table->foreignId('lab_values_id')->nullable()->constrained('lab_values')->onDelete('cascade');
            $table->foreignId('adl_id')->nullable()->constrained('act_of_daily_living')->onDelete('cascade');
            $table->foreignId('vitals_id')->nullable()->constrained('vital_signs')->onDelete('cascade');
            $table->foreignId('vital_signs_id')->nullable()->constrained('vital_signs')->onDelete('cascade');
            
            $table->text('diagnosis');
            $table->text('diagnosis_alert')->nullable();
            $table->text('planning')->nullable();
            $table->text('planning_alert')->nullable();
            $table->text('intervention')->nullable();
            $table->text('intervention_alert')->nullable();
            $table->text('evaluation')->nullable();
            $table->text('evaluation_alert')->nullable();
            $table->string('rule_file_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nursing_diagnoses');
    }
};