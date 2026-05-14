<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_statistics', function (Blueprint $table): void {
            $table->id();
            $table->date('statistic_date')->unique();
            $table->unsignedInteger('active_subscriptions')->default(0);
            $table->unsignedInteger('expired_subscriptions')->default(0);
            $table->unsignedInteger('cancelled_subscriptions')->default(0);
            $table->unsignedInteger('upgrades')->default(0);
            $table->unsignedInteger('downgrades')->default(0);
            $table->decimal('churn_rate', 5, 2)->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();

            $table->index('statistic_date', 'ss_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_statistics');
    }
};
