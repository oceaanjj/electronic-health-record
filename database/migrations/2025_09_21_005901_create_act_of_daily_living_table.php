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
        Schema::create('act_of_daily_living', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients', 'patient_id')->onDelete('cascade');
            $table->integer('day_no')->nullable();
            $table->date('date')->nullable();
            $table->string('mobility_assessment')->nullable();
            $table->string('hygiene_assessment')->nullable();
            $table->string('toileting_assessment')->nullable();
            $table->string('feeding_assessment')->nullable();
            $table->string('hydration_assessment')->nullable();
            $table->string('sleep_pattern_assessment')->nullable();
            $table->string('pain_level_assessment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities_of_daily_living');
    }
};
