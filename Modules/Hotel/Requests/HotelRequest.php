<?php

namespace Modules\Hotel\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HotelRequest extends FormRequest
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
        $hotel = $this->route('hotel');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'alpha_dash',
                'max:255',
                Rule::unique('hotels', 'slug')
                    ->where('organization_id', $organization->id)
                    ->ignore($hotel?->id),
            ],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:5000'],
            'address' => ['nullable', 'string', 'max:2000'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'size:2', Rule::in(array_keys(config('countries', [])))],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'timezone' => ['required', 'timezone', 'max:64'],
            'currency' => ['required', 'string', 'size:3'],
            'checkin_time' => ['nullable', 'date_format:H:i'],
            'checkout_time' => ['nullable', 'date_format:H:i'],
            'star_rating' => ['nullable', 'integer', 'between:1,5'],
            'status' => ['required', Rule::in(['active', 'inactive', 'maintenance', 'suspended'])],
        ];
    }
}
