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
            // Change columns to be nullable
            $table->text('planning')->nullable()->change();
            $table->text('intervention')->nullable()->change();
            $table->text('evaluation')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nursing_diagnoses', function (Blueprint $table) {
            // Revert columns to be not nullable
            $table->text('planning')->nullable(false)->change();
            $table->text('intervention')->nullable(false)->change();
            $table->text('evaluation')->nullable(false)->change();
        });
    }
};