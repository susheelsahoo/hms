<?php

namespace Modules\Subscription\Listeners;

use Modules\Subscription\Events\InvoiceGenerated;
use Modules\Subscription\Jobs\SendInvoiceEmail;

class SendInvoiceNotification
{
    public function handle(InvoiceGenerated $event): void
    {
        // Dispatch async job to send invoice email
        SendInvoiceEmail::dispatch($event->invoice);
    }
}
