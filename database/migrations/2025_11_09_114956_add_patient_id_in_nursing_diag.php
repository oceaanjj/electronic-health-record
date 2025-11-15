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
            $table->string('patient_id')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nursing_diagnoses', function (Blueprint $table) {
            $table->dropColumn('patient_id');
        });
    }
};
