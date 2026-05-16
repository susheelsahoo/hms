<?php

namespace Modules\Room\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('manage_rooms') === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'room_number' => ['required', 'string', 'max:30'],
            'floor_number' => ['nullable', 'string', 'max:30'],
            'capacity' => ['required', 'integer', 'min:1', 'max:50'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'status' => ['required', Rule::in(['available', 'occupied', 'reserved', 'cleaning', 'maintenance'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
