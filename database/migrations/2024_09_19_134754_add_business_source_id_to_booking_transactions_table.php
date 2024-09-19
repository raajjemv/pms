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
            $table->foreignId('business_source_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_transactions', function (Blueprint $table) {
            $table->dropForeign('business_source_id');
            $table->dropColumn('business_source_id');
        });
    }
};
