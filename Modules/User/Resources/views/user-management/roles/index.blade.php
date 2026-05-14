@extends('layouts.authenticated')

@section('title', 'Roles | HMS')
@section('page-title', 'Roles')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">Role Management</h1>
            <p class="text-secondary mb-0">Create roles and assign permissions.</p>
        </div>
        <a href="{{ route('user-management.roles.create') }}" class="btn btn-primary align-self-md-start">Create Role</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Permissions</th>
                        <th>Users</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $role->name }}</div>
                                <div class="small text-secondary">{{ $role->description ?: 'N/A' }}</div>
                            </td>
                            <td><code>{{ $role->slug }}</code></td>
                            <td>{{ $role->permissions_count }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('user-management.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('user-management.roles.destroy', $role) }}" class="d-inline" onsubmit="return confirm('Delete this role?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" @disabled($role->users_count > 0)>Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-5">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $roles->links() }}</div>
@endsection
