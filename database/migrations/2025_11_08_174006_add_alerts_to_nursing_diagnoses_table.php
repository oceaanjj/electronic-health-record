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
        // This adds the new columns to your existing table
        Schema::table('nursing_diagnoses', function (Blueprint $table) {
            // We make them nullable() so they can be empty
            // We put them 'after' the related field for organization
            $table->text('diagnosis_alert')->nullable()->after('diagnosis');
            $table->text('planning_alert')->nullable()->after('planning');
            $table->text('intervention_alert')->nullable()->after('intervention');
            $table->text('evaluation_alert')->nullable()->after('evaluation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nursing_diagnoses', function (Blueprint $table) {
            // This lets you undo the migration if needed
            $table->dropColumn([
                'diagnosis_alert',
                'planning_alert',
                'intervention_alert',
                'evaluation_alert'
            ]);
        });
    }
};