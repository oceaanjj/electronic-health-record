<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
           $table->foreignId('patient_id')->constrained('patients', 'patient_id')->onDelete('cascade');
            $table->date('date');
            $table->time('time');
            $table->integer('day_no')->nullable();
            $table->string('temperature')->nullable();
            $table->string('hr')->nullable();
            $table->string('rr')->nullable();
            $table->string('bp')->nullable();
            $table->string('spo2')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};