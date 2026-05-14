<?php

namespace Modules\Subscription\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpgradeSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'subscription_id' => 'required|exists:subscriptions,id',
            'new_plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'prorate' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'subscription_id.required' => 'Subscription is required.',
            'subscription_id.exists' => 'Subscription not found.',
            'new_plan_id.required' => 'New plan is required.',
            'new_plan_id.exists' => 'Selected plan does not exist.',
        ];
    }
}
