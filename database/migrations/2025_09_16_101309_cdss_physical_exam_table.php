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
        Schema::create('cdss_physical_exam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('physical_exam_id')->constrained('physical_exams')->onDelete('cascade');
            $table->foreignId('patient_id')->references('patient_id')->on('patients')->onDelete('cascade');
            $table->json('alerts')->nullable();
            $table->enum('risk_level', ['low', 'moderate', 'high', 'critical'])->default('low');
            $table->boolean('requires_immediate_attention')->default(false);
            $table->json('abnormal_findings')->nullable();
            $table->json('triggered_rules')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cdss_physical_exam');
    }
};
