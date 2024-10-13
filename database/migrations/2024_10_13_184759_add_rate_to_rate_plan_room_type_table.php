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
        Schema::table('rate_plan_room_type', function (Blueprint $table) {
            $table->decimal('rate', 10, 2)->nullable();
            $table->boolean('default')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rate_plan_room_type', function (Blueprint $table) {
            $table->dropColumn(['rate', 'default']);
        });
    }
};
