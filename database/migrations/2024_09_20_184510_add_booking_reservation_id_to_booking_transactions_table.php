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
        Schema::table('booking_transactions', function (Blueprint $table) {
            $table->foreignId('booking_reservation_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_transactions', function (Blueprint $table) {
            $table->dropForeign('booking_reservation_id');
            $table->dropColumn('booking_reservation_id');
        });
    }
};
