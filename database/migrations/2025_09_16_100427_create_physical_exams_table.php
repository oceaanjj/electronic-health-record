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
        Schema::create('physical_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients', 'patient_id')->onDelete('cascade');
            $table->string('general_appearance')->nullable();
            $table->string('skin_condition')->nullable();
            $table->string('eye_condition')->nullable();
            $table->string('oral_condition')->nullable();
            $table->string('cardiovascular')->nullable();
            $table->string('abdomen_condition')->nullable();
            $table->string('extremities')->nullable();
            $table->string('neurological')->nullable();
            //    Alerts columns
            $table->string('general_appearance_alert')->nullable();
            $table->string('skin_alert')->nullable();
            $table->string('eye_alert')->nullable();
            $table->string('oral_alert')->nullable();
            $table->string('cardiovascular_alert')->nullable();
            $table->string('abdomen_alert')->nullable();
            $table->string('extremities_alert')->nullable();
            $table->string('neurological_alert')->nullable();




            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('physical_exams');
    }
};
