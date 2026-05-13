<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_id')->nullable()->comment('Gateway or bank transaction reference.');
            $table->string('payment_method', 50);
            $table->string('payment_gateway', 80)->nullable();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'failed', 'refunded'])->default('pending');
            $table->jsonb('gateway_response')->default('{}');
            $table->timestampTz('paid_at')->nullable();
            $table->jsonb('metadata')->default('{}');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['organization_id', 'hotel_id', 'payment_status', 'deleted_at']);
            $table->index(['booking_id', 'payment_status']);
            $table->index(['payment_gateway', 'transaction_id']);
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
