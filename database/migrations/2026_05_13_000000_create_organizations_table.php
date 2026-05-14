<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->comment('Tenant-safe public identifier.');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('logo')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 2)->nullable()->comment('ISO 3166-1 alpha-2 country code.');
            $table->string('zip_code', 20)->nullable();
            $table->string('timezone', 64)->default('UTC');
            $table->char('currency', 3)->default('USD')->comment('ISO 4217 currency code.');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['status', 'deleted_at'], 'org_status_idx');
            $table->index(['country', 'state', 'city'], 'org_location_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
