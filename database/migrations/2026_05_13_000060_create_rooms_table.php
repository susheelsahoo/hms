<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->restrictOnDelete();
            $table->string('room_number', 30);
            $table->string('floor_number', 30)->nullable();
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->decimal('price', 12, 2)->nullable()->comment('Override price; falls back to room type base_price when null.');
            $table->enum('status', ['available', 'occupied', 'reserved', 'cleaning', 'maintenance'])->default('available');
            $table->text('notes')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->unique(['hotel_id', 'room_number']);
            $table->index(['organization_id', 'hotel_id', 'status', 'deleted_at']);
            $table->index(['hotel_id', 'room_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
