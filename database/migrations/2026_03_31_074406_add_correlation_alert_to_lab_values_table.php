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
        Schema::table('lab_values', function (Blueprint $table) {
            $table->text('correlation_alert')->nullable()->after('basophils_alert');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lab_values', function (Blueprint $table) {
            $table->dropColumn('correlation_alert');
        });
    }
};
