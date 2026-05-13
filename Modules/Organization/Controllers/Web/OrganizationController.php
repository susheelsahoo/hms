<?php

namespace Modules\Organization\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Organization\Models\Organization;
use Modules\Organization\Requests\OrganizationRequest;

class OrganizationController
{
    public function index(): View
    {
        return view('organizations.index', [
            'organizations' => Organization::query()
                ->withCount(['hotels', 'users'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('organizations.create', [
            'organization' => new Organization([
                'country' => 'US',
                'timezone' => config('countries.US.timezone', 'UTC'),
                'currency' => config('countries.US.currency', 'USD'),
                'status' => 'active',
            ]),
        ]);
    }

    public function store(OrganizationRequest $request): RedirectResponse
    {
        Organization::query()->create($this->payload($request));

        return redirect()
            ->route('super-admin.organizations.index')
            ->with('status', 'Organization created successfully.');
    }

    public function edit(Organization $organization): View
    {
        return view('organizations.edit', [
            'organization' => $organization,
        ]);
    }

    public function update(OrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $organization->update($this->payload($request));

        return redirect()
            ->route('super-admin.organizations.index')
            ->with('status', 'Organization updated successfully.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $organization->delete();

        return redirect()
            ->route('super-admin.organizations.index')
            ->with('status', 'Organization deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(OrganizationRequest $request): array
    {
        $validated = $request->validated();
        $country = strtoupper($validated['country']);
        $countryDefaults = config("countries.{$country}", []);

        $validated['slug'] = ($validated['slug'] ?? null) ?: Str::slug($validated['name']);
        $validated['country'] = $country;
        $validated['currency'] = $countryDefaults['currency'] ?? 'USD';
        $validated['timezone'] = $countryDefaults['timezone'] ?? 'UTC';
        $validated['metadata'] = [];

        return $validated;
    }
}
