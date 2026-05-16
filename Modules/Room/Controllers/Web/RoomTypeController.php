<?php

namespace Modules\Room\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Services\HotelService;
use Modules\Room\Models\RateType;
use Modules\Room\Models\RoomType;
use Modules\Room\Requests\RoomTypeRequest;

class RoomTypeController
{
    public function __construct(
        private HotelService $hotelService
    ) {}

    public function index(): View|RedirectResponse
    {
        $hotel = $this->selectedHotel();

        if (! $hotel) {
            return $this->redirectToHotelSelection();
        }

        return view('room::room-types.index', [
            'hotel' => $hotel,
            'roomTypes' => $hotel->roomTypes()
                ->with(['rateType'])
                ->withCount('rooms')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): View|RedirectResponse
    {
        $hotel = $this->selectedHotel();

        if (! $hotel) {
            return $this->redirectToHotelSelection();
        }

        return view('room::room-types.create', [
            'hotel' => $hotel,
            'roomType' => new RoomType([
                'max_adults' => 1,
                'max_children' => 0,
                'base_price' => 0,
            ]),
            'rateTypes' => $hotel->rateTypes()->orderBy('name')->get(),
        ]);
    }

    public function store(RoomTypeRequest $request): RedirectResponse
    {
        $hotel = $this->selectedHotelOrFail();
        $payload = $this->payload($request, $hotel);

        $hotel->roomTypes()->create($payload);

        return redirect()
            ->route('room-management.room-types.index')
            ->with('status', 'Room type created successfully.');
    }

    public function edit(RoomType $roomType): View
    {
        $hotel = $this->selectedHotelOrFail();
        $this->ensureRoomTypeBelongsToHotel($roomType, $hotel);

        return view('room::room-types.edit', [
            'hotel' => $hotel,
            'roomType' => $roomType,
            'rateTypes' => $hotel->rateTypes()->orderBy('name')->get(),
        ]);
    }

    public function update(RoomTypeRequest $request, RoomType $roomType): RedirectResponse
    {
        $hotel = $this->selectedHotelOrFail();
        $this->ensureRoomTypeBelongsToHotel($roomType, $hotel);

        $roomType->update($this->payload($request, $hotel, $roomType));

        return redirect()
            ->route('room-management.room-types.index')
            ->with('status', 'Room type updated successfully.');
    }

    public function destroy(RoomType $roomType): RedirectResponse
    {
        $hotel = $this->selectedHotelOrFail();
        $this->ensureRoomTypeBelongsToHotel($roomType, $hotel);

        if ($roomType->rooms()->exists()) {
            return back()->withErrors(['room_type' => 'Room type cannot be deleted while rooms are assigned to it.']);
        }

        $roomType->delete();

        return redirect()
            ->route('room-management.room-types.index')
            ->with('status', 'Room type deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(RoomTypeRequest $request, Hotel $hotel, ?RoomType $roomType = null): array
    {
        $validated = $request->validated();
        $validated['slug'] = ($validated['slug'] ?? null) ?: Str::slug($validated['name']);
        $validated['organization_id'] = $hotel->organization_id;
        $validated['metadata'] = [];

        $rateTypeId = $validated['rate_type_id'] ?? null;
        $rateType = null;
        if ($rateTypeId !== null) {
            $rateType = RateType::query()
                ->where('hotel_id', $hotel->id)
                ->whereKey($rateTypeId)
                ->first();

            if (! $rateType) {
                throw ValidationException::withMessages([
                    'rate_type_id' => 'The selected rate type is not available for this hotel.',
                ]);
            }
        }

        if (! array_key_exists('base_price', $validated) || $validated['base_price'] === null) {
            $validated['base_price'] = $rateType ? $rateType->base_rate : 0;
        }

        $duplicate = RoomType::query()
            ->where('hotel_id', $hotel->id)
            ->where('slug', $validated['slug'])
            ->when($roomType, fn ($query) => $query->whereKeyNot($roomType->id))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'slug' => 'The room type slug has already been taken for this hotel.',
            ]);
        }

        return $validated;
    }

    private function selectedHotel(): ?Hotel
    {
        $hotelId = session('selected_hotel_id');

        if (! $hotelId) {
            return null;
        }

        $hotel = Hotel::query()->find($hotelId);

        if (! $hotel || ! $this->hotelService->canAccessHotel(auth()->user(), $hotel)) {
            session()->forget(['selected_hotel_id', 'selected_hotel_name']);

            return null;
        }

        return $hotel;
    }

    private function selectedHotelOrFail(): Hotel
    {
        return $this->selectedHotel() ?? abort(404, 'Please select a hotel first.');
    }

    private function redirectToHotelSelection(): RedirectResponse
    {
        return redirect()
            ->route('hotels.list')
            ->with('status', 'Please select a hotel before managing rooms.');
    }

    private function ensureRoomTypeBelongsToHotel(RoomType $roomType, Hotel $hotel): void
    {
        abort_if($roomType->hotel_id !== $hotel->id, 404);
    }
}
