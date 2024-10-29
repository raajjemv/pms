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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained();
            $table->foreignId('room_type_id')->constrained();
            $table->foreignId('room_class_id')->constrained();
            $table->string('room_number');
            $table->integer('capacity')->default(2);
            $table->integer('maximum_occupancy')->default(2);
            $table->string('room_size');
            $table->foreignId('bed_type_id')->constrained();
            $table->foreignId('bathroom_type_id')->nullable()->constrained();
            $table->foreignId('room_view_id')->nullable()->constrained();
            $table->json('amenities')->nullable();
            $table->boolean('smoking')->default(0);
            $table->string('floor_number')->nullable();
            $table->foreignId('room_status_id')->constrained();
            $table->text('room_description');
            $table->boolean('family_room')->default(0);
            $table->string('family_room_id')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
