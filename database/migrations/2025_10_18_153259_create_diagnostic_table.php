<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('diagnostic', function (Blueprint $table) {
            $table->id();
            
            // Link to the 'patients' table using your primary key 'patient_id'
            $table->foreignId('patient_id')->constrained(
                table: 'patients', column: 'patient_id'
            )->onDelete('cascade');
            
            // Link to the user (nurse) who uploaded the image
            $table->foreignId('uploader_id')->constrained('users')->onDelete('cascade');

            // Image Details
            $table->string('diagnostic_type'); // 'X-RAY', 'ULTRASOUND', etc.
            $table->string('path');
            $table->string('filename');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('diagnostic');
    }
};