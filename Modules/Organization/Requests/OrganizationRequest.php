<?php

namespace Modules\Organization\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $organization = $this->route('organization');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'alpha_dash',
                'max:255',
                Rule::unique('organizations', 'slug')->ignore($organization?->id),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'logo' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'size:2', Rule::in(array_keys(config('countries', [])))],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'status' => ['required', Rule::in(['active', 'inactive', 'suspended'])],
            'subscription_plan_id' => ['required', 'integer', Rule::exists('subscription_plans', 'id')->where('is_active', true)],
        ];
    }
}
