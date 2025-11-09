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
        Schema::table('nursing_diagnoses', function (Blueprint $table) {

            // Add patient_id for patient-level diagnoses
            $table->foreignId('patient_id')
                ->nullable()
                ->constrained('patients') // Assumes your patients table is 'patients'
                ->onDelete('cascade')
                ->after('intake_and_output_id'); // Places it after the other foreign IDs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nursing_diagnoses', function (Blueprint $table) {
            // Must drop the constraint before the column
            $table->dropForeign(['patient_id']);
            $table->dropColumn('patient_id');
        });
    }
};