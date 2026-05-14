<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenue_statistics', function (Blueprint $table): void {
            $table->id();
            $table->date('statistic_date')->unique();
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->decimal('monthly_revenue', 14, 2)->default(0);
            $table->decimal('annual_revenue', 14, 2)->default(0);
            $table->decimal('refunds', 14, 2)->default(0);
            $table->unsignedInteger('failed_payments')->default(0);
            $table->unsignedInteger('pending_payments')->default(0);
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();

            $table->index('statistic_date', 'rs_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_statistics');
    }
};
