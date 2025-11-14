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
        Schema::table('patients', function (Blueprint $table) {
            $table->string('room_no')->nullable()->after('admission_date');
            $table->string('bed_no')->nullable()->after('room_no');
            $table->string('contact_name')->nullable()->after('bed_no');
            $table->string('contact_relationship')->nullable()->after('contact_name');
            $table->string('contact_number')->nullable()->after('contact_relationship');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['room_no', 'bed_no', 'contact_name', 'contact_relationship', 'contact_number']);
        });
    }
};
