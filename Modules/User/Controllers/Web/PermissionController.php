<?php

namespace Modules\User\Controllers\Web;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\User\Models\Permission;
use Modules\User\Requests\PermissionRequest;

class PermissionController
{
    public function index(): View
    {
        return view('user::user-management.permissions.index', [
            'permissions' => Permission::query()
                ->withCount('roles')
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('user::user-management.permissions.create', [
            'permission' => new Permission(),
        ]);
    }

    public function store(PermissionRequest $request): RedirectResponse
    {
        Permission::query()->create($request->validated());

        return redirect()
            ->route('user-management.permissions.index')
            ->with('status', 'Permission created successfully.');
    }

    public function edit(Permission $permission): View
    {
        return view('user::user-management.permissions.edit', [
            'permission' => $permission,
        ]);
    }

    public function update(PermissionRequest $request, Permission $permission): RedirectResponse
    {
        $permission->update($request->validated());

        return redirect()
            ->route('user-management.permissions.index')
            ->with('status', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return redirect()
            ->route('user-management.permissions.index')
            ->with('status', 'Permission deleted successfully.');
    }
}
