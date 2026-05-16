<?php

namespace Modules\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Modules\Hotel\Models\Hotel;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_staff') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $passwordRules = $user ? ['nullable', 'string', 'min:8', 'confirmed'] : ['required', 'string', 'min:8', 'confirmed'];

        return [
            'organization_id' => ['nullable', 'integer', 'exists:organizations,id'],
            'hotel_ids' => ['nullable', 'array'],
            'hotel_ids.*' => ['integer', 'distinct', 'exists:hotels,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => $passwordRules,
            'status' => ['required', Rule::in(['active', 'inactive', 'invited', 'suspended'])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $organizationId = $this->input('organization_id');
            $hotelIds = collect($this->input('hotel_ids', []))
                ->filter()
                ->map(fn ($hotelId) => (int) $hotelId)
                ->unique()
                ->values();

            if ($hotelIds->isEmpty()) {
                return;
            }

            if (blank($organizationId)) {
                $validator->errors()->add('hotel_ids', 'Select an organization before assigning hotels.');

                return;
            }

            $validHotelCount = Hotel::query()
                ->where('organization_id', (int) $organizationId)
                ->whereIn('id', $hotelIds)
                ->count();

            if ($validHotelCount !== $hotelIds->count()) {
                $validator->errors()->add('hotel_ids', 'Selected hotels must belong to the selected organization.');
            }
        });
    }
}
