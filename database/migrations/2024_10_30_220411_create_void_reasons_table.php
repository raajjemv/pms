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
        Schema::create('void_reasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants');
            $table->string('reason');
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('locked')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('void_reason_id')->nullable();
        });
        Schema::table('booking_reservations', function (Blueprint $table) {
            $table->foreignId('void_reason_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('void_reasons');
    }
};
