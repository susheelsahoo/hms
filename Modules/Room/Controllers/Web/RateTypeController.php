<?php

namespace Modules\Room\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Services\HotelService;
use Modules\Room\Models\RateType;
use Modules\Room\Requests\RateTypeRequest;

class RateTypeController
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

        return view('room::rate-types.index', [
            'hotel' => $hotel,
            'rateTypes' => RateType::query()
                ->where('hotel_id', $hotel->id)
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

        return view('room::rate-types.create', [
            'hotel' => $hotel,
            'rateType' => new RateType([
                'base_rate' => 0,
            ]),
        ]);
    }

    public function store(RateTypeRequest $request): RedirectResponse
    {
        $hotel = $this->selectedHotelOrFail();

        $payload = $this->payload($request, $hotel);
        RateType::query()->create($payload);

        return redirect()
            ->route('room-management.rate-types.index')
            ->with('status', 'Rate type created successfully.');
    }

    public function edit(RateType $rateType): View
    {
        $hotel = $this->selectedHotelOrFail();
        $this->ensureRateTypeBelongsToHotel($rateType, $hotel);

        return view('room::rate-types.edit', [
            'hotel' => $hotel,
            'rateType' => $rateType,
        ]);
    }

    public function update(RateTypeRequest $request, RateType $rateType): RedirectResponse
    {
        $hotel = $this->selectedHotelOrFail();
        $this->ensureRateTypeBelongsToHotel($rateType, $hotel);

        $rateType->update($this->payload($request, $hotel, $rateType));

        return redirect()
            ->route('room-management.rate-types.index')
            ->with('status', 'Rate type updated successfully.');
    }

    public function destroy(RateType $rateType): RedirectResponse
    {
        $hotel = $this->selectedHotelOrFail();
        $this->ensureRateTypeBelongsToHotel($rateType, $hotel);

        if ($rateType->roomTypes()->exists()) {
            return back()->withErrors(['rate_type' => 'Rate type cannot be deleted while room types are assigned to it.']);
        }

        $rateType->delete();

        return redirect()
            ->route('room-management.rate-types.index')
            ->with('status', 'Rate type deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(RateTypeRequest $request, Hotel $hotel, ?RateType $rateType = null): array
    {
        $validated = $request->validated();
        $validated['slug'] = ($validated['slug'] ?? null) ?: Str::slug($validated['name']);
        $validated['organization_id'] = $hotel->organization_id;
        $validated['hotel_id'] = $hotel->id;
        $validated['metadata'] = [];

        $duplicate = RateType::query()
            ->where('hotel_id', $hotel->id)
            ->where('slug', $validated['slug'])
            ->when($rateType, fn ($query) => $query->whereKeyNot($rateType->id))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'slug' => 'The rate type slug has already been taken for this hotel.',
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
            ->with('status', 'Please select a hotel before managing rate types.');
    }

    private function ensureRateTypeBelongsToHotel(RateType $rateType, Hotel $hotel): void
    {
        abort_if($rateType->hotel_id !== $hotel->id, 404);
    }
}

