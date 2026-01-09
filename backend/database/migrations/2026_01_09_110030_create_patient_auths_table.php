<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_auths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('invitation_status', 16)->default('none'); // none|pending|accepted|expired|revoked
            $table->string('invitation_token_hash', 64)->nullable()->unique();
            $table->dateTime('invitation_expires_at')->nullable()->index();
            $table->dateTime('invited_at')->nullable();
            $table->dateTime('accepted_at')->nullable();

            $table->timestamps();

            $table->unique(['patient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_auths');
    }
};
