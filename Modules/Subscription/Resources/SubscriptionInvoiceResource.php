<?php

namespace Modules\Subscription\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'organization_id' => $this->organization_id,
            'subscription_id' => $this->subscription_id,
            'amount' => (float) $this->amount,
            'tax_amount' => (float) $this->tax_amount,
            'total_amount' => (float) $this->total_amount,
            'currency' => $this->currency,
            'status' => $this->status->value,
            'invoice_date' => $this->invoice_date->toDateString(),
            'due_date' => $this->due_date->toDateString(),
            'paid_at' => $this->paid_at?->toIso8601String(),
            'payment_method' => $this->payment_method,
            'is_pending' => $this->isPending(),
            'is_paid' => $this->isPaid(),
            'is_overdue' => $this->isOverdue(),
            'download_url' => $this->getInvoiceUrl(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
