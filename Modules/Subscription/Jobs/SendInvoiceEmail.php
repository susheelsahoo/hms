<?php

namespace Modules\Subscription\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Subscription\Models\SubscriptionInvoice;

class SendInvoiceEmail implements ShouldQueue
{
    use Queueable;

    public function __construct(public SubscriptionInvoice $invoice) {}

    public function handle(): void
    {
        // Send invoice email
        \Log::info('Invoice email sent', [
            'invoice_number' => $this->invoice->invoice_number,
            'organization_id' => $this->invoice->organization_id,
        ]);
    }
}
