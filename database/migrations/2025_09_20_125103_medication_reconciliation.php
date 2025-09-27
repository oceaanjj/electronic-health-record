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
        Schema::create('current_medication', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients', 'patient_id')->onDelete('cascade');
            $table->string('current_med')->nullable();
            $table->string('current_dose')->nullable();
            $table->string('current_route')->nullable();
            $table->string('current_frequency')->nullable();
            $table->string('current_indication')->nullable();
            $table->string('current_text')->nullable();
            $table->timestamps();
        });

        Schema::create('home_medication', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients', 'patient_id')->onDelete('cascade');
            $table->string('home_med')->nullable();
            $table->string('home_dose')->nullable();
            $table->string('home_route')->nullable();
            $table->string('home_frequency')->nullable();
            $table->string('home_indication')->nullable();
            $table->string('home_text')->nullable();
            $table->timestamps();
        });

        Schema::create('changes_in_medication', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients', 'patient_id')->onDelete('cascade');
            $table->string('change_med')->nullable();
            $table->string('change_dose')->nullable();
            $table->string('change_route')->nullable();
            $table->string('change_frequency')->nullable();
            $table->string('change_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('current_medication');
        Schema::dropIfExists('home_medication');
        Schema::dropIfExists('changes_in_medication');
    }
};
