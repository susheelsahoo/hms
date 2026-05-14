<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_daily', function (Blueprint $table): void {
            $table->id();
            $table->date('analytics_date')->unique();
            $table->unsignedInteger('total_organizations')->default(0);
            $table->unsignedInteger('total_hotels')->default(0);
            $table->unsignedInteger('total_rooms')->default(0);
            $table->unsignedInteger('total_users')->default(0);
            $table->unsignedInteger('total_bookings')->default(0);
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->unsignedInteger('active_subscriptions')->default(0);
            $table->unsignedInteger('trial_subscriptions')->default(0);
            $table->unsignedInteger('cancellations')->default(0);
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();

            $table->index('analytics_date', 'ad_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_daily');
    }
};
