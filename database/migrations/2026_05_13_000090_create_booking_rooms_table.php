<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_rooms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->restrictOnDelete();
            $table->decimal('price_per_night', 12, 2);
            $table->unsignedSmallInteger('total_nights');
            $table->decimal('subtotal', 12, 2);
            $table->timestampsTz();

            $table->unique(['booking_id', 'room_id']);
            $table->index(['organization_id', 'booking_id'], 'br_booking_idx');
            $table->index(['room_id', 'created_at'], 'br_room_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_rooms');
    }
};
