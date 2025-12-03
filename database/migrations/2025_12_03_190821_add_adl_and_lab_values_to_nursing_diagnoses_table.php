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
        Schema::table('nursing_diagnoses', function (Blueprint $table) {
            if (!Schema::hasColumn('nursing_diagnoses', 'adl_id')) {
                $table->foreignId('adl_id')->nullable()->constrained('act_of_daily_living')->onDelete('cascade');
            }
            if (!Schema::hasColumn('nursing_diagnoses', 'lab_values_id')) {
                $table->foreignId('lab_values_id')->nullable()->constrained('lab_values')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nursing_diagnoses', function (Blueprint $table) {
            if (Schema::hasColumn('nursing_diagnoses', 'adl_id')) {
                $table->dropForeign(['adl_id']);
                $table->dropColumn('adl_id');
            }
            if (Schema::hasColumn('nursing_diagnoses', 'lab_values_id')) {
                $table->dropForeign(['lab_values_id']);
                $table->dropColumn('lab_values_id');
            }
        });
    }
};
