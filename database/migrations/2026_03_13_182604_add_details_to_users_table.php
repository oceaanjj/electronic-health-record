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
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name')->after('username')->nullable();
            $table->date('birthdate')->after('full_name')->nullable();
            $table->integer('age')->after('birthdate')->nullable();
            $table->string('sex')->after('age')->nullable();
            $table->text('address')->after('sex')->nullable();
            $table->string('birthplace')->after('address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['full_name', 'birthdate', 'age', 'sex', 'address', 'birthplace']);
        });
    }
};
