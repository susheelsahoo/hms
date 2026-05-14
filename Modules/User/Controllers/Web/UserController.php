<?php

namespace Modules\User\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Modules\Organization\Models\Organization;
use Modules\Role\Models\Role;
use Modules\User\Models\User;
use Modules\User\Requests\UserRequest;

class UserController
{
    public function index(): View
    {
        return view('user::user-management.users.index', [
            'users' => User::query()
                ->with(['organization', 'role'])
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('user::user-management.users.create', [
            'user' => new User(['status' => 'active']),
            'roles' => Role::query()->orderBy('name')->get(),
            'organizations' => Organization::query()->orderBy('name')->get(),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        User::query()->create($this->payload($request));

        return redirect()
            ->route('user-management.users.index')
            ->with('status', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('user::user-management.users.edit', [
            'user' => $user,
            'roles' => Role::query()->orderBy('name')->get(),
            'organizations' => Organization::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->update($this->payload($request));

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
            return Arr::except($validated, ['password', 'password_confirmation']);
        }

        return Arr::except($validated, ['password_confirmation']);
    }
}
