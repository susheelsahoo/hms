<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained('subscriptions')->cascadeOnDelete();
            $table->string('invoice_number', 50)->unique()->comment('Unique invoice identifier.');
            $table->decimal('amount', 12, 2)->default(0)->comment('Subscription amount.');
            $table->decimal('tax_amount', 12, 2)->default(0)->comment('Tax amount.');
            $table->decimal('total_amount', 12, 2)->default(0)->comment('Total including tax.');
            $table->char('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->index();
            $table->date('invoice_date')->comment('Invoice generation date.');
            $table->date('due_date')->comment('Payment due date.');
            $table->dateTimeTz('paid_at')->nullable()->comment('Payment completion date.');
            $table->string('payment_method', 50)->nullable()->comment('Payment gateway used.');
            $table->string('transaction_id', 100)->nullable()->comment('Payment gateway transaction ID.');
            $table->jsonb('metadata')->default('{}')->comment('Payment gateway response and metadata.');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['organization_id', 'status'], 'si_org_status_idx');
            $table->index(['status', 'due_date'], 'si_status_due_idx');
            $table->index('invoice_date', 'si_invoice_date_idx');
            $table->index('payment_method', 'si_payment_method_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
    }
};
