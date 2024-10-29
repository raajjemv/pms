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
        Schema::create('channel_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('name');
            $table->boolean('default')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('channel_groups');
    }
};
