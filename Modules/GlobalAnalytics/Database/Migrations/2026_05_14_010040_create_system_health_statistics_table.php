<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_health_statistics', function (Blueprint $table): void {
            $table->id();
            $table->date('statistic_date')->unique();
            $table->unsignedInteger('api_response_time')->default(0);
            $table->unsignedInteger('queue_jobs_pending')->default(0);
            $table->unsignedInteger('failed_jobs')->default(0);
            $table->decimal('cache_hit_rate', 5, 2)->default(0);
            $table->unsignedInteger('database_connections')->default(0);
            $table->unsignedInteger('slow_queries')->default(0);
            $table->decimal('error_rate', 5, 2)->default(0);
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();

            $table->index('statistic_date', 'shs_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_health_statistics');
    }
};
