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
        Schema::create('booking_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('booking_id')->constrained('bookings');
            $table->foreignId('room_id')->constrained('rooms');
            $table->string('booking_customer')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->tinyInteger('adults')->default(0);
            $table->tinyInteger('children')->default(0);
            $table->foreignId('rate_plan_id')->nullable()->constrained();
            $table->dateTime('from');
            $table->dateTime('to');
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->boolean('master')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_reservations');
    }
};
