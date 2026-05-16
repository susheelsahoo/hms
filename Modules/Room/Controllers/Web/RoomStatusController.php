<?php

namespace Modules\Room\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Services\HotelService;
use Modules\Room\Models\Room;

class RoomStatusController
{
    public function __construct(
        private HotelService $hotelService
    ) {}

    public function index(): View|RedirectResponse
    {
        $hotel = $this->selectedHotel();

        if (! $hotel) {
            return redirect()
                ->route('hotels.list')
                ->with('status', 'Please select a hotel before managing room statuses.');
        }

        return view('room::statuses.index', [
            'hotel' => $hotel,
            'rooms' => $hotel->rooms()
                ->with('roomType')
                ->orderBy('room_number')
                ->get()
                ->groupBy('status'),
            'statuses' => $this->statuses(),
        ]);
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        abort_if(! $request->user()?->hasPermission('manage_rooms'), 403);

        $hotel = $this->selectedHotelOrFail();
        abort_if($room->hotel_id !== $hotel->id, 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys($this->statuses()))],
        ]);

        $room->update(['status' => $validated['status']]);

        return back()->with('status', 'Room status updated successfully.');
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
}
