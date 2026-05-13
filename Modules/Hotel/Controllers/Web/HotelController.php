<?php

namespace Modules\Hotel\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Requests\HotelRequest;
use Modules\Organization\Models\Organization;

class HotelController
{
    public function index(Organization $organization): View
    {
        return view('hotels.index', [
            'organization' => $organization,
            'hotels' => $organization->hotels()->latest()->paginate(15),
        ]);
    }

    public function create(Organization $organization): View
    {
        return view('hotels.create', [
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

        return view('hotels.edit', [
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
