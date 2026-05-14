<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->foreignId('old_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            $table->foreignId('new_plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            $table->enum('action', [
                'created',
                'upgrade',
                'downgrade',
                'renewal',
                'cancellation',
                'reactivation',
                'suspension',
                'expiration',
                'trial_started',
                'trial_ended'
            ])->index();
            $table->text('description')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete()->comment('User who made the change.');
            $table->jsonb('metadata')->default('{}')->comment('Additional context data.');
            $table->timestampsTz();

            $table->index(['organization_id', 'action'], 'sh_org_action_idx');
            $table->index(['subscription_id', 'created_at'], 'sh_sub_created_idx');
            $table->index('action', 'sh_action_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_histories');
    }
};
