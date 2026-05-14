<?php

namespace Modules\User\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Role\Models\Role;
use Modules\User\Models\Permission;
use Modules\User\Requests\RoleRequest;

class RoleController
{
    public function index(): View
    {
        return view('user::user-management.roles.index', [
            'roles' => Role::query()
                ->withCount(['users', 'permissions'])
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('user::user-management.roles.create', [
            'role' => new Role(),
            'permissions' => Permission::query()->orderBy('name')->get(),
            'selectedPermissions' => [],
        ]);
    }

    public function store(RoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $permissions = $validated['permissions'] ?? [];
        unset($validated['permissions']);

        $role = Role::query()->create($validated);
        $role->permissions()->sync($permissions);

        return redirect()
            ->route('user-management.roles.index')
            ->with('status', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        return view('user::user-management.roles.edit', [
            'role' => $role,
            'permissions' => Permission::query()->orderBy('name')->get(),
            'selectedPermissions' => $role->permissions()->pluck('permissions.id')->all(),
        ]);
    }

    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $validated = $request->validated();
        $permissions = $validated['permissions'] ?? [];
        unset($validated['permissions']);

        $role->update($validated);
        $role->permissions()->sync($permissions);

        return redirect()
            ->route('user-management.roles.index')
            ->with('status', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if($role->users()->exists(), 422, 'Roles assigned to users cannot be deleted.');

        $role->delete();

        return redirect()
            ->route('user-management.roles.index')
            ->with('status', 'Role deleted successfully.');
    }
}
