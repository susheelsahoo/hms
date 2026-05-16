<?php

namespace Modules\Room\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RateTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_rate_types') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'base_rate' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ];
    }
}

