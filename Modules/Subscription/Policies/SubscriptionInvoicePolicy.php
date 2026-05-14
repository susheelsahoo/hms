<?php

namespace Modules\Subscription\Policies;

use App\Models\User;
use Modules\Subscription\Models\SubscriptionInvoice;

class SubscriptionInvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->organization_id !== null;
    }

    public function view(User $user, SubscriptionInvoice $invoice): bool
    {
        return $user->organization_id === $invoice->organization_id;
    }

    public function download(User $user, SubscriptionInvoice $invoice): bool
    {
        return $user->organization_id === $invoice->organization_id;
    }
}
