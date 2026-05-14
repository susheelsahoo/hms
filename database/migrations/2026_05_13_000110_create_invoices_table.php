<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number', 50);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['draft', 'issued', 'paid', 'void', 'overdue'])->default('draft');
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->unique(['hotel_id', 'invoice_number']);
            $table->index(['organization_id', 'hotel_id', 'status', 'deleted_at'], 'inv_status_idx');
            $table->index(['booking_id', 'status'], 'inv_booking_idx');
            $table->index('due_date', 'inv_due_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
