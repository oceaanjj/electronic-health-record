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
        Schema::create('diagnostic_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients', 'patient_id')->onDelete('cascade');
            $table->enum('image_type', ['xray', 'ultrasound', 'ct_scan_mri', 'echocardiogram']);
            $table->string('storage/app/public/diagnostic_images'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnostic_images');
    }
};
