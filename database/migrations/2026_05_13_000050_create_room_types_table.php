<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('max_adults')->default(1);
            $table->unsignedSmallInteger('max_children')->default(0);
            $table->decimal('base_price', 12, 2)->default(0);
            $table->string('size', 50)->nullable();
            $table->string('bed_type', 80)->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->unique(['hotel_id', 'slug']);
            $table->index(['organization_id', 'hotel_id', 'deleted_at'], 'rtype_hotel_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
