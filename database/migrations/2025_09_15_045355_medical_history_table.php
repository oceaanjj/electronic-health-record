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
        Schema::create('medical_history', function (Blueprint $table) {    
            $table->id('medical_id');
            $table->unsignedBigInteger('id');

            $table->enum('type', [
            'present_illness',
            'past_medical_surgical',
            'allergies',
            'vaccination'
             ]);
            $table->string('condition_name')->nullable();
            $table->text('description')->nullable();
            $table->text('medication')->nullable();
            $table->text('dosage')->nullable();
            $table->text('side_effect')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('developmental_history', function (Blueprint $table) {         
            $table->unsignedBigInteger('id');
            $table->text('gross_motor')->nullable();
            $table->text('fine_motor')->nullable();
            $table->text('language')->nullable();
            $table->text('cognitive')->nullable();
            $table->text('social')->nullable();
            $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_history');
    }
};
