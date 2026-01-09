<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('status', 32)->nullable(); // active, trialing, past_due, canceled, unpaid, incomplete, etc
            $table->string('plan_key', 16)->nullable(); // BASIC|PRO
            $table->string('stripe_customer_id', 64)->nullable()->index();
            $table->string('stripe_subscription_id', 64)->nullable()->index();
            $table->dateTime('current_period_end')->nullable();
            $table->json('limits_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

