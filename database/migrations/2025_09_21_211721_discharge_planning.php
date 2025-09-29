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
         Schema::create('discharge_planning', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients', 'patient_id')->onDelete('cascade');
            $table->string('criteria_feverRes')->nullable();
            $table->string('criteria_patientCount')->nullable();
            $table->string('criteria_manageFever')->nullable();
            $table->string('criteria_manageFever2')->nullable();
            $table->string('instruction_med')->nullable();
            $table->string('instruction_appointment')->nullable();
            $table->string('instruction_fluidIntake')->nullable();
            $table->string('instruction_exposure')->nullable();
            $table->string('instruction_complications')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discharge_planning');
    }
};
