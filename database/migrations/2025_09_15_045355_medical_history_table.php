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
        Schema::create('medical_history', function (Blueprint $table) {
            $table->bigInt('medical_history_id')->primary();            
            $table->id();
            $table->string('condition_name')->nullable();
            $table->text('condition_description')->nullable();
            $table->string('medication_name')->nullable();
            $table->string('medication_dosage')->nullable();
            $table->string('side_effects')->nullable();
            $table->text('medication_comments')->nullable();
            $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_history');
    }
};
