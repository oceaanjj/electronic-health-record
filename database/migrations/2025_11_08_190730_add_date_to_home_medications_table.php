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
        Schema::table('home_medication', function (Blueprint $table) { // Corrected table name
            $table->date('date')->nullable()->after('patient_id'); // Add the new date column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('home_medication', function (Blueprint $table) { // Corrected table name
            $table->dropColumn('date'); // Drop the date column if rolling back
        });
    }
};
