<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('slug')->unique()->comment('URL-friendly plan identifier.');
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 12, 2)->default(0)->comment('Monthly subscription price.');
            $table->decimal('price_yearly', 12, 2)->default(0)->comment('Yearly subscription price.');
            $table->unsignedSmallInteger('hotel_limit')->default(1)->comment('Maximum hotels allowed under this plan.');
            $table->unsignedSmallInteger('staff_limit')->default(5)->comment('Maximum staff members allowed.');
            $table->unsignedInteger('room_limit')->default(50)->comment('Maximum rooms allowed.');
            $table->unsignedInteger('booking_limit')->default(1000)->comment('Monthly booking limit.');
            $table->unsignedSmallInteger('storage_limit')->default(10)->comment('Storage limit in GB.');
            $table->jsonb('features')->default('{}')->comment('Plan features JSON.');
            $table->boolean('is_trial')->default(false)->comment('Is this a trial plan?');
            $table->unsignedSmallInteger('trial_days')->default(14)->comment('Trial period in days.');
            $table->boolean('is_active')->default(true);
            $table->jsonb('metadata')->default('{}')->comment('Flexible metadata for future extensions.');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index('slug', 'sp_slug_idx');
            $table->index('is_active', 'sp_active_idx');
            $table->index(['is_active', 'is_trial'], 'sp_active_trial_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
