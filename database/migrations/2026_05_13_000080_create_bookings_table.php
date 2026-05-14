<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->constrained()->restrictOnDelete();
            $table->string('booking_number', 40)->comment('Hotel-scoped external booking reference.');
            $table->string('source', 50)->default('direct');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedSmallInteger('total_guests')->default(1);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('booking_status', ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->unique(['hotel_id', 'booking_number']);
            $table->index(
                ['organization_id', 'hotel_id', 'booking_status', 'deleted_at'],
                'bk_status_idx'
            );
            $table->index(
                ['hotel_id', 'check_in_date', 'check_out_date'],
                'bk_date_idx'
            );
            $table->index(['hotel_id', 'payment_status'], 'bk_payment_idx');
            $table->index(['guest_id', 'created_at'], 'bk_guest_idx');
            $table->index('created_by', 'bk_creator_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
