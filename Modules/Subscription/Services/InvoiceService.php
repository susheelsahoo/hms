<?php

namespace Modules\Subscription\Services;

use Illuminate\Support\Facades\DB;
use Modules\Subscription\Models\Subscription;
use Modules\Subscription\Models\SubscriptionInvoice;
use Modules\Subscription\Enums\InvoiceStatus;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * Generate invoice for subscription
     */
    public function generateInvoice(Subscription $subscription): SubscriptionInvoice
    {
        return DB::transaction(function () use ($subscription) {
            $plan = $subscription->plan;
            $amount = $subscription->amount;
            $taxAmount = $this->calculateTax($amount);
            $invoiceNumber = $this->generateInvoiceNumber($subscription->organization_id);

            return SubscriptionInvoice::create([
                'organization_id' => $subscription->organization_id,
                'subscription_id' => $subscription->id,
                'invoice_number' => $invoiceNumber,
                'amount' => $amount,
                'tax_amount' => $taxAmount,
                'total_amount' => $amount + $taxAmount,
                'currency' => $subscription->currency,
                'status' => InvoiceStatus::PENDING,
                'invoice_date' => now(),
                'due_date' => now()->addDays(14),
                'metadata' => [
                    'plan_name' => $plan->name,
                    'billing_cycle' => $subscription->billing_cycle,
                ],
            ]);
        });
    }

    /**
     * Generate prorated invoice for upgrades
     */
    public function generateProratedInvoice(Subscription $subscription, float $oldAmount, float $newAmount): SubscriptionInvoice
    {
        return DB::transaction(function () use ($subscription, $oldAmount, $newAmount) {
            $proratedAmount = $this->calculateProration($subscription, $newAmount);
            $taxAmount = $this->calculateTax($proratedAmount);
            $invoiceNumber = $this->generateInvoiceNumber($subscription->organization_id);

            return SubscriptionInvoice::create([
                'organization_id' => $subscription->organization_id,
                'subscription_id' => $subscription->id,
                'invoice_number' => $invoiceNumber,
                'amount' => $proratedAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $proratedAmount + $taxAmount,
                'currency' => $subscription->currency,
                'status' => InvoiceStatus::PENDING,
                'invoice_date' => now(),
                'due_date' => now()->addDays(7),
                'metadata' => [
                    'type' => 'proration',
                    'old_amount' => $oldAmount,
                    'new_amount' => $newAmount,
                    'proration_calculation' => 'line-item-proration',
                ],
            ]);
        });
    }

    /**
     * Calculate tax amount
     */
    private function calculateTax(float $amount, float $taxRate = 0.10): float
    {
        return round($amount * $taxRate, 2);
    }

    /**
     * Calculate prorated amount
     */
    private function calculateProration(Subscription $subscription, float $newAmount): float
    {
        $daysRemaining = now()->diffInDays($subscription->ends_at, absolute: false);
        $totalDaysInBillingCycle = $subscription->ends_at->diffInDays($subscription->starts_at);
        
        if ($totalDaysInBillingCycle === 0) {
            return $newAmount;
        }

        $daysUsed = $totalDaysInBillingCycle - $daysRemaining;
        $proratedAmount = ($newAmount / $totalDaysInBillingCycle) * $daysRemaining;

        return round($proratedAmount, 2);
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(int $organizationId): string
    {
        $prefix = 'INV';
        $date = now()->format('YmdHis');
        $suffix = random_int(1000, 9999);

        return "{$prefix}-{$date}-{$suffix}";
    }

    /**
     * Get invoice by number
     */
    public function getByInvoiceNumber(string $invoiceNumber): ?SubscriptionInvoice
    {
        return SubscriptionInvoice::where('invoice_number', $invoiceNumber)->first();
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(SubscriptionInvoice $invoice, string $transactionId, string $method = null): void
    {
        $invoice->markAsPaid($transactionId, $method);
    }

    /**
     * Get organization's invoices
     */
    public function getOrganizationInvoices(int $organizationId, int $limit = 50)
    {
        return SubscriptionInvoice::where('organization_id', $organizationId)
            ->latest()
            ->paginate($limit);
    }

    /**
     * Get overdue invoices
     */
    public function getOverdueInvoices()
    {
        return SubscriptionInvoice::overdue()
            ->with('subscription', 'organization')
            ->get();
    }

    /**
     * Send invoice reminder
     */
    public function sendReminder(SubscriptionInvoice $invoice): void
    {
        // This will be called by a job/listener
        // Implementation depends on notification preferences
    }
}
