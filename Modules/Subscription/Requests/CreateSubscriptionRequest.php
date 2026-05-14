<?php

namespace Modules\Subscription\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'organization_id' => 'required|exists:organizations,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'auto_renew' => 'boolean',
            'metadata' => 'nullable|json',
        ];
    }

    public function messages(): array
    {
        return [
            'organization_id.required' => 'Organization is required.',
            'organization_id.exists' => 'Selected organization does not exist.',
            'subscription_plan_id.required' => 'Subscription plan is required.',
            'subscription_plan_id.exists' => 'Selected plan does not exist.',
            'billing_cycle.required' => 'Billing cycle is required.',
            'billing_cycle.in' => 'Billing cycle must be monthly or yearly.',
        ];
    }
}
