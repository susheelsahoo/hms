<?php

namespace Modules\Hotel\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Requests\HotelRequest;
use Modules\Hotel\Services\HotelService;
use Modules\Organization\Models\Organization;

class HotelController
{
    public function __construct(
        private HotelService $hotelService
    ) {}

    public function index(Organization $organization): View
    {
        return view('hotel::hotels.index', [
            'organization' => $organization,
            'hotels' => $organization->hotels()->latest()->paginate(15),
        ]);
    }

    public function create(Organization $organization): View
    {
        return view('hotel::hotels.create', [
            'organization' => $organization,
            'hotel' => new Hotel([
                'country' => $organization->country ?: 'US',
                'timezone' => $organization->timezone ?: config('countries.US.timezone', 'UTC'),
                'currency' => $organization->currency ?: config('countries.US.currency', 'USD'),
                'checkin_time' => '14:00',
                'checkout_time' => '11:00',
                'status' => 'active',
            ]),
        ]);
    }

    public function store(HotelRequest $request, Organization $organization): RedirectResponse
    {
        $organization->hotels()->create($this->payload($request));

        return redirect()
            ->route('super-admin.organizations.hotels.index', $organization)
            ->with('status', 'Hotel created successfully.');
    }

    public function edit(Organization $organization, Hotel $hotel): View
    {
        $this->ensureHotelBelongsToOrganization($organization, $hotel);

        return view('hotel::hotels.edit', [
            'organization' => $organization,
            'hotel' => $hotel,
        ]);
    }

    public function update(HotelRequest $request, Organization $organization, Hotel $hotel): RedirectResponse
    {
        $this->ensureHotelBelongsToOrganization($organization, $hotel);

        $hotel->update($this->payload($request));

        return redirect()
            ->route('super-admin.organizations.hotels.index', $organization)
            ->with('status', 'Hotel updated successfully.');
    }

    public function destroy(Organization $organization, Hotel $hotel): RedirectResponse
    {
        $this->ensureHotelBelongsToOrganization($organization, $hotel);

        $hotel->delete();

        return redirect()
            ->route('super-admin.organizations.hotels.index', $organization)
            ->with('status', 'Hotel deleted successfully.');
    }

    /**
     * List hotels for the current user (organization owner or hotel manager)
     */
    public function listUserHotels(): View
    {
        $user = auth()->user();
        $hotels = $this->hotelService->getUserHotels($user);
        $selectedHotelId = session('selected_hotel_id');
        $selectedHotel = null;

        if ($selectedHotelId) {
            $selectedHotel = $hotels->firstWhere('id', $selectedHotelId);
        }

        return view('hotel::hotels.list-user-hotels', [
            'hotels' => $hotels,
            'selectedHotel' => $selectedHotel,
        ]);
    }

    /**
     * Select a hotel for the current user
     */
    public function selectHotel(Hotel $hotel): RedirectResponse
    {
        $user = auth()->user();

        // Check if user can access this hotel
        if (!$this->hotelService->canAccessHotel($user, $hotel)) {
            abort(403, 'You do not have access to this hotel.');
        }

        // Store selected hotel in session
        session(['selected_hotel_id' => $hotel->id, 'selected_hotel_name' => $hotel->name]);

        return redirect()->route('dashboard')->with('status', 'Hotel switched to ' . $hotel->name);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(HotelRequest $request): array
    {
        $validated = $request->validated();
        $validated['slug'] = ($validated['slug'] ?? null) ?: Str::slug($validated['name']);
        $validated['country'] = strtoupper($validated['country']);
        $validated['currency'] = strtoupper($validated['currency']);
        $validated['metadata'] = [];

        return $validated;
    }

    private function ensureHotelBelongsToOrganization(Organization $organization, Hotel $hotel): void
    {
        abort_if($hotel->organization_id !== $organization->id, 404);
    }
}

