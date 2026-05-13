<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_hotels', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->enum('access_type', ['owner', 'admin', 'manager', 'staff'])->default('staff');
            $table->boolean('is_primary')->default(false);
            $table->timestampsTz();

            $table->unique(['user_id', 'hotel_id']);
            $table->index(['organization_id', 'hotel_id', 'access_type']);
            $table->index(['organization_id', 'user_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_hotels');
    }
};
