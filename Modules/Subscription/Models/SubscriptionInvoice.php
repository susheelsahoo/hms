<?php

namespace Modules\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Subscription\Enums\InvoiceStatus;

class SubscriptionInvoice extends Model
{
    use SoftDeletes;

    protected $table = 'subscription_invoices';

    protected $fillable = [
        'organization_id',
        'subscription_id',
        'invoice_number',
        'amount',
        'tax_amount',
        'total_amount',
        'currency',
        'status',
        'invoice_date',
        'due_date',
        'paid_at',
        'payment_method',
        'transaction_id',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'status' => InvoiceStatus::class,
        'metadata' => 'json',
    ];

    // Relationships
    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', InvoiceStatus::PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', InvoiceStatus::PAID);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', InvoiceStatus::FAILED);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->date())
            ->where('status', InvoiceStatus::PENDING);
    }

    public function scopeByPaymentMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    // Methods
    public function isPending(): bool
    {
        return $this->status === InvoiceStatus::PENDING;
    }

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatus::PAID;
    }

    public function isFailed(): bool
    {
        return $this->status === InvoiceStatus::FAILED;
    }

    public function isOverdue(): bool
    {
        return $this->isPending() && now()->date()->gt($this->due_date);
    }

    public function markAsPaid(string $transactionId, string $method = null): void
    {
        $this->update([
            'status' => InvoiceStatus::PAID,
            'paid_at' => now(),
            'transaction_id' => $transactionId,
            'payment_method' => $method,
        ]);
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => InvoiceStatus::FAILED,
            'metadata' => array_merge($this->metadata ?? [], ['failure_reason' => $reason]),
        ]);
    }

    public function refund(): void
    {
        $this->update(['status' => InvoiceStatus::REFUNDED]);
    }

    public function getTotalAmount(): float
    {
        return (float) $this->total_amount;
    }

    public function getInvoiceUrl(): string
    {
        return route('subscriptions.invoices.download', $this->invoice_number);
    }
}
