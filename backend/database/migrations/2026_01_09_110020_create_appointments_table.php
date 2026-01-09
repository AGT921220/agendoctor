<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->unsignedSmallInteger('duration_minutes');
            $table->string('status', 16);
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->index(['starts_at']);
            $table->index(['patient_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

