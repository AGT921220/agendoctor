<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_settings', function (Blueprint $table) {
            $table->id();
            $table->string('timezone')->default('America/Mexico_City');
            $table->unsignedSmallInteger('default_appointment_duration_minutes')->default(30);
            $table->unsignedSmallInteger('buffer_minutes')->default(0);
            $table->unsignedSmallInteger('confirm_cancel_cutoff_hours')->default(12);
            $table->timestamps();
        });

        Schema::create('practice_schedule_blocks', function (Blueprint $table) {
            $table->id();
            // 1 = Monday ... 7 = Sunday
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            $table->index(['day_of_week']);
        });

        Schema::create('practice_holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_holidays');
        Schema::dropIfExists('practice_schedule_blocks');
        Schema::dropIfExists('practice_settings');
    }
};

