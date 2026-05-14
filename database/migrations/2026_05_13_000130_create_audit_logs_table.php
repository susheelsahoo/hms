<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('module', 80);
            $table->string('action', 80);
            $table->string('entity_type');
            $table->string('entity_id', 64)->comment('String keeps UUID and bigint entity IDs compatible.');
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->jsonb('payload')->default('{}');
            $table->timestampsTz();

            $table->index(['organization_id', 'module', 'action'], 'al_module_idx');
            $table->index(['entity_type', 'entity_id'], 'al_entity_idx');
            $table->index(['user_id', 'created_at'], 'al_user_idx');
            $table->index('created_at', 'al_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
