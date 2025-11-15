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


        if (Schema::hasTable('intake_and_outputs')) {
            Schema::dropIfExists('intake_and_outputs');
            Schema::create('intake_and_outputs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained('patients', 'patient_id')->onDelete('cascade');
                $table->integer('day_no')->nullable();
                $table->date('date')->nullable();
                $table->integer('oral_intake')->nullable();
                $table->integer('iv_fluids_volume')->nullable();
                $table->string('iv_fluids_type')->nullable();
                $table->integer('urine_output')->nullable();
                $table->string('cdss_alerts')->nullable();
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intake_and_outputs');
    }



};
