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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_type');
            $table->foreignId('tenant_id')->constrained();
            $table->string('booking_number');
            $table->foreignId('room_id')->constrained();
            $table->date('from');
            $table->date('to');
            $table->string('booking_customer')->nullable();
            $table->string('billing_customer')->nullable();
            $table->string('billing_customer_email')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained();
            $table->string('status')->default('pending');
            // $table->string('payment_status')->default('not_paid');
            // $table->decimal('booking_rate', 10, 2);
            $table->tinyInteger('adults')->default(0);
            $table->tinyInteger('children')->default(0);
            $table->foreignId('user_id')->constrained();
            $table->foreignId('rate_plan_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
