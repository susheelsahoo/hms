<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_usages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->unsignedSmallInteger('hotels_used')->default(0)->comment('Current hotels count.');
            $table->unsignedSmallInteger('staff_used')->default(0)->comment('Current staff count.');
            $table->unsignedInteger('rooms_used')->default(0)->comment('Current rooms count.');
            $table->unsignedInteger('bookings_used')->default(0)->comment('Current month bookings.');
            $table->unsignedSmallInteger('storage_used')->default(0)->comment('Storage used in GB.');
            $table->dateTime('usage_period_start')->comment('Usage period start.');
            $table->dateTime('usage_period_end')->comment('Usage period end.');
            $table->jsonb('metadata')->default('{}')->comment('Additional tracking data.');
            $table->timestampsTz();

            $table->unique(['organization_id', 'subscription_id']);
            $table->index(['organization_id', 'subscription_id'], 'su_org_sub_idx');
            $table->index('usage_period_start', 'su_period_start_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_usages');
    }
};
