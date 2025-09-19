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

    Schema::create('present_illness', function (Blueprint $table) {    
        $table->id('medical_id');
        $table->string('condition_name')->nullable();
        $table->text('description')->nullable();
        $table->text('medication')->nullable();
        $table->text('dosage')->nullable();
        $table->text('side_effect')->nullable();
        $table->text('comment')->nullable();
        $table->timestamps();
    });

    Schema::create('past_medical_surgical', function (Blueprint $table) {    
        $table->id('medical_id');
        $table->string('condition_name')->nullable();
        $table->text('description')->nullable();
        $table->text('medication')->nullable();
        $table->text('dosage')->nullable();
        $table->text('side_effect')->nullable();
        $table->text('comment')->nullable();
        $table->timestamps();
    });

    Schema::create('allergies', function (Blueprint $table) {    
        $table->id('medical_id');
        $table->string('condition_name')->nullable();
        $table->text('description')->nullable();
        $table->text('medication')->nullable();
        $table->text('dosage')->nullable();
        $table->text('side_effect')->nullable();
        $table->text('comment')->nullable();
        $table->timestamps();
    });

    Schema::create('vaccination', function (Blueprint $table) {    
        $table->id('medical_id');
        $table->string('condition_name')->nullable();
        $table->text('description')->nullable();
        $table->text('medication')->nullable();
        $table->text('dosage')->nullable();
        $table->text('side_effect')->nullable();
        $table->text('comment')->nullable();
        $table->timestamps();
    });

        Schema::create('developmental_history', function (Blueprint $table) {         
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
        Schema::dropIfExists('developmental_history');
        Schema::dropIfExists('vaccination');
        Schema::dropIfExists('allergies');
        Schema::dropIfExists('past_medical_surgical');
        Schema::dropIfExists('present_illness');

    }
};
