<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_statistics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();
            $table->date('statistic_date');
            $table->unsignedInteger('total_bookings')->default(0);
            $table->unsignedInteger('completed_bookings')->default(0);
            $table->unsignedInteger('cancelled_bookings')->default(0);
            $table->unsignedInteger('no_show_bookings')->default(0);
            $table->decimal('total_revenue', 14, 2)->default(0);
            $table->decimal('average_booking_value', 12, 2)->default(0);
            $table->decimal('occupancy_rate', 5, 2)->default(0);
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();

            $table->unique(['organization_id', 'hotel_id', 'statistic_date'], 'bs_scope_date_unique');
            $table->index('statistic_date', 'bs_date_idx');
            $table->index(['organization_id', 'statistic_date'], 'bs_org_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_statistics');
    }
};
