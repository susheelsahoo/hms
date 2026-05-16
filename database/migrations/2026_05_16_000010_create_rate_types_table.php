<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rate_types', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->decimal('base_rate', 12, 2)->default(0);
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->unique(['hotel_id', 'slug']);
            $table->index(['organization_id', 'hotel_id', 'deleted_at'], 'rate_hotel_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_types');
    }
};

