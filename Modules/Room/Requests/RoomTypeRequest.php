<?php

namespace Modules\Room\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_room_types') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'max_adults' => ['required', 'integer', 'min:1', 'max:20'],
            'max_children' => ['required', 'integer', 'min:0', 'max:20'],
            'rate_type_id' => ['nullable', 'integer', 'exists:rate_types,id'],
            'base_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'size' => ['nullable', 'string', 'max:50'],
            'bed_type' => ['nullable', 'string', 'max:80'],
        ];
    }
}
