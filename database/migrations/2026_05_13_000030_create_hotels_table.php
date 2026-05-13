<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('timezone', 64)->default('UTC');
            $table->char('currency', 3)->default('USD');
            $table->timeTz('checkin_time')->nullable();
            $table->timeTz('checkout_time')->nullable();
            $table->unsignedTinyInteger('star_rating')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance', 'suspended'])->default('active');
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->unique(['organization_id', 'slug']);
            $table->index(['organization_id', 'status', 'deleted_at']);
            $table->index(['country', 'state', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
