<?php

namespace Modules\User\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Modules\Hotel\Models\Hotel;
use Modules\Organization\Models\Organization;
use Modules\Role\Models\Role;
use Modules\User\Models\User;
use Modules\User\Requests\UserRequest;

class UserController
{
    public function index(Request $request): View
    {
        $organizationId = $request->query('organization_id');

        return view('user::user-management.users.index', [
            'users' => User::query()
                ->with(['organization', 'role'])
                ->when($organizationId === 'platform', fn ($query) => $query->whereNull('organization_id'))
                ->when(is_numeric($organizationId), fn ($query) => $query->where('organization_id', (int) $organizationId))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'organizations' => Organization::query()->orderBy('name')->get(),
            'selectedOrganizationId' => $organizationId,
        ]);
    }

    public function create(): View
    {
        return view('user::user-management.users.create', [
            'user' => new User(['status' => 'active']),
            'roles' => Role::query()->orderBy('name')->get(),
            'organizations' => Organization::query()->orderBy('name')->get(),
            'hotelsByOrganization' => $this->hotelsByOrganization(),
            'selectedHotelIds' => [],
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::query()->create($this->payload($request));

        $this->syncUserHotels($user, $request);

        return redirect()
            ->route('user-management.users.index')
            ->with('status', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('user::user-management.users.edit', [
            'user' => $user->load('hotels'),
            'roles' => Role::query()->orderBy('name')->get(),
            'organizations' => Organization::query()->orderBy('name')->get(),
            'hotelsByOrganization' => $this->hotelsByOrganization(),
            'selectedHotelIds' => $user->hotels->pluck('id')->map(fn ($id) => (int) $id)->all(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->update($this->payload($request));
        $this->syncUserHotels($user, $request);

        return redirect()
            ->route('user-management.users.index')
            ->with('status', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 422, 'You cannot delete your own account.');

        $user->delete();

        return redirect()
            ->route('user-management.users.index')
            ->with('status', 'User deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(UserRequest $request): array
    {
        $validated = $request->validated();
        $validated['organization_id'] = blank($validated['organization_id'] ?? null)
            ? null
            : $validated['organization_id'];

        if (blank($validated['password'] ?? null)) {
            return Arr::except($validated, ['hotel_ids', 'password', 'password_confirmation']);
        }

        return Arr::except($validated, ['hotel_ids', 'password_confirmation']);
    }

    /**
     * @return array<int, array<int, array{id: int, name: string}>>
     */
    private function hotelsByOrganization(): array
    {
        return Hotel::query()
            ->orderBy('name')
            ->get(['id', 'organization_id', 'name'])
            ->groupBy('organization_id')
            ->map(fn ($hotels) => $hotels
                ->map(fn (Hotel $hotel) => [
                    'id' => (int) $hotel->id,
                    'name' => $hotel->name,
                ])
                ->values()
                ->all())
            ->all();
    }

    private function syncUserHotels(User $user, UserRequest $request): void
    {
        $organizationId = $user->organization_id;
        $hotelIds = collect($request->validated('hotel_ids', []))
            ->map(fn ($hotelId) => (int) $hotelId)
            ->unique()
            ->values();

        if (! $organizationId || $hotelIds->isEmpty()) {
            $user->hotels()->sync([]);

            return;
        }

        $accessType = $this->hotelAccessType($user->role);
        $syncData = $hotelIds
            ->mapWithKeys(fn (int $hotelId, int $index) => [
                $hotelId => [
                    'organization_id' => $organizationId,
                    'access_type' => $accessType,
                    'is_primary' => $index === 0,
                ],
            ])
            ->all();

        $user->hotels()->sync($syncData);
    }

    private function hotelAccessType(?Role $role): string
    {
        return match ($role?->slug) {
            Role::SUPER_ADMIN, Role::ORGANIZATION_OWNER => 'owner',
            Role::HOTEL_ADMIN => 'admin',
            Role::HOTEL_MANAGER => 'manager',
            default => 'staff',
        };
    }
}
