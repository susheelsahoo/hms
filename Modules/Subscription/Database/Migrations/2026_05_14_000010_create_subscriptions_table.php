<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->unique()->constrained()->cascadeOnDelete()->comment('One subscription per organization.');
            $table->foreignId('subscription_plan_id')->constrained()->restrictOnDelete();
            $table->enum('status', [
                'trial',
                'active',
                'past_due',
                'expired',
                'cancelled',
                'suspended'
            ])->default('trial')->index()->comment('Subscription status.');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly')->comment('Billing frequency.');
            $table->dateTimeTz('starts_at')->comment('Subscription start date.');
            $table->dateTimeTz('ends_at')->comment('Subscription end date.');
            $table->dateTimeTz('trial_ends_at')->nullable()->comment('Trial period end date.');
            $table->dateTimeTz('grace_ends_at')->nullable()->comment('Grace period end date for past due.');
            $table->dateTimeTz('cancelled_at')->nullable()->comment('Cancellation date.');
            $table->dateTimeTz('renewal_at')->nullable()->comment('Next renewal date.');
            $table->decimal('amount', 12, 2)->default(0)->comment('Current subscription amount.');
            $table->char('currency', 3)->default('USD')->comment('ISO 4217 currency code.');
            $table->boolean('auto_renew')->default(true)->comment('Auto-renewal enabled?');
            $table->jsonb('metadata')->default('{}')->comment('Payment gateway metadata, coupon info, etc.');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['organization_id', 'status'], 'sub_org_status_idx');
            $table->index(['status', 'ends_at'], 'sub_status_end_idx');
            $table->index('renewal_at', 'sub_renewal_idx');
            $table->index(['created_at', 'status'], 'sub_created_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
