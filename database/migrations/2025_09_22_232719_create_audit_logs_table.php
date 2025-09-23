<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('audit_id'); // primary key
            $table->unsignedBigInteger('id')->nullable(); // user reference
            $table->string('action'); // action type (singular to match model)
            $table->text('details')->nullable(); // additional details
            $table->timestamps(); // created_at and updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
