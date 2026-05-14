<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')
                ->nullable()
                ->comment('Nullable for platform super admins.')
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('role_id')->constrained()->restrictOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone', 30)->nullable();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->timestampTz('email_verified_at')->nullable();
            $table->timestampTz('last_login_at')->nullable();
            $table->enum('status', ['active', 'inactive', 'invited', 'suspended'])->default('active');
            $table->jsonb('metadata')->default('{}');
            $table->rememberToken();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['organization_id', 'role_id'], 'usr_org_role_idx');
            $table->index(['organization_id', 'status', 'deleted_at'], 'usr_status_idx');
            $table->index('last_login_at', 'usr_login_idx');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestampTz('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
