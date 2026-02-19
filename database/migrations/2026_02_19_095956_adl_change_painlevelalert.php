<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('act_of_daily_living', function (Blueprint $table) {
            $table->text('pain_level_alert')->change();
        });
    }

    public function down(): void
    {
        Schema::table('act_of_daily_living', function (Blueprint $table) {
            $table->string('pain_level_alert', 255)->change();
        });
    }
};