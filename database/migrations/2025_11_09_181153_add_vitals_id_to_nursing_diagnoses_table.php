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
            if (!Schema::hasColumn('nursing_diagnoses', 'vital_signs_id')) {
                $table->foreignId('vital_signs_id')->nullable()->constrained('vital_signs')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nursing_diagnoses', function (Blueprint $table) {
            $table->dropForeign(['vitals_id']);
            $table->dropColumn('vitals_id');
        });
    }
};
