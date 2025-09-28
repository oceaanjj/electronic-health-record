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
            $table->foreignId('physical_exam_id')->nullable()->constrained('physical_exams')->onDelete('cascade');
            $table->foreignId('intake_and_output_id')->nullable()->constrained('intake_and_outputs')->onDelete('cascade');
            $table->text('diagnosis');
            $table->text('planning');
            $table->text('intervention');
            $table->text('evaluation');
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
