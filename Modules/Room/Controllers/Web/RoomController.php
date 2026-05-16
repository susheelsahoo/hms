<?php

namespace Modules\Room\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Services\HotelService;
use Modules\Room\Models\Room;
use Modules\Room\Requests\RoomRequest;

class RoomController
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

        return view('room::rooms.index', [
            'hotel' => $hotel,
            'rooms' => $hotel->rooms()
                ->with('roomType')
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

        return view('room::rooms.create', [
            'hotel' => $hotel,
            'room' => new Room([
                'capacity' => 1,
                'status' => 'available',
            ]),
            'roomTypes' => $hotel->roomTypes()->orderBy('name')->get(),
            'statuses' => $this->statuses(),
        ]);
    }

    public function store(RoomRequest $request): RedirectResponse
    {
        $hotel = $this->selectedHotelOrFail();
        $payload = $this->payload($request, $hotel);

        $hotel->rooms()->create($payload);

        return redirect()
            ->route('room-management.rooms.index')
            ->with('status', 'Room created successfully.');
    }

    public function edit(Room $room): View
    {
        $hotel = $this->selectedHotelOrFail();
        $this->ensureRoomBelongsToHotel($room, $hotel);

        return view('room::rooms.edit', [
            'hotel' => $hotel,
            'room' => $room,
            'roomTypes' => $hotel->roomTypes()->orderBy('name')->get(),
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        $hotel = $this->selectedHotelOrFail();
        $this->ensureRoomBelongsToHotel($room, $hotel);

        $room->update($this->payload($request, $hotel, $room));

        return redirect()
            ->route('room-management.rooms.index')
            ->with('status', 'Room updated successfully.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $hotel = $this->selectedHotelOrFail();
        $this->ensureRoomBelongsToHotel($room, $hotel);
        $room->delete();

        return redirect()
            ->route('room-management.rooms.index')
            ->with('status', 'Room deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(RoomRequest $request, Hotel $hotel, ?Room $room = null): array
    {
        $validated = $request->validated();

        $roomType = $hotel->roomTypes()
            ->whereKey($validated['room_type_id'])
            ->first();

        if (! $roomType) {
            throw ValidationException::withMessages([
                'room_type_id' => 'The selected room type is not available for this hotel.',
            ]);
        }

        $duplicate = Room::query()
            ->where('hotel_id', $hotel->id)
            ->where('room_number', $validated['room_number'])
            ->when($room, fn ($query) => $query->whereKeyNot($room->id))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'room_number' => 'The room number has already been taken for this hotel.',
            ]);
        }

        $validated['organization_id'] = $hotel->organization_id;
        $validated['metadata'] = [];

        return $validated;
    }

    /**
     * @return array<string, string>
     */
    private function statuses(): array
    {
        return [
            'available' => 'Available',
            'occupied' => 'Occupied',
            'reserved' => 'Reserved',
            'cleaning' => 'Cleaning',
            'maintenance' => 'Maintenance',
        ];
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

    private function ensureRoomBelongsToHotel(Room $room, Hotel $hotel): void
    {
        abort_if($room->hotel_id !== $hotel->id, 404);
    }
}
