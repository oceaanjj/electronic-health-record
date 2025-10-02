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
        Schema::create('patients', function (Blueprint $table) {
            $table->id('patient_id');
            $table->string('name');
            $table->integer('age');
            $table->enum('sex', ['Male', 'Female', 'Other']);
            $table->string('address')->nullable();
            $table->string('birthplace')->nullable();
            $table->string('religion', 100)->nullable();
            $table->string('ethnicity', 100)->nullable();
            $table->text('chief_complaints')->nullable();
            $table->date('admission_date');

            //
            $table->foreignId('user_id')->constrained()->after('admission_date');


            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
