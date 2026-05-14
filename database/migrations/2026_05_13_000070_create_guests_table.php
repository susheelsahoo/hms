<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('nationality', 2)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->string('id_type', 50)->nullable();
            $table->string('id_number')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['organization_id', 'hotel_id', 'deleted_at'], 'gst_hotel_idx');
            $table->index(['hotel_id', 'email'], 'gst_email_idx');
            $table->index(['hotel_id', 'phone'], 'gst_phone_idx');
            $table->index(['id_type', 'id_number'], 'gst_id_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
