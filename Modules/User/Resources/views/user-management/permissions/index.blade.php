@extends('layouts.authenticated')

@section('title', 'Permissions | HMS')
@section('page-title', 'Permissions')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">Permission Management</h1>
            <p class="text-secondary mb-0">Manage authorization capabilities assigned to roles.</p>
        </div>
        <a href="{{ route('user-management.permissions.create') }}" class="btn btn-primary align-self-md-start">Create Permission</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Roles</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permissions as $permission)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $permission->name }}</div>
                                <div class="small text-secondary">{{ $permission->description ?: 'N/A' }}</div>
                            </td>
                            <td><code>{{ $permission->slug }}</code></td>
                            <td>{{ $permission->roles_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('user-management.permissions.edit', $permission) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('user-management.permissions.destroy', $permission) }}" class="d-inline" onsubmit="return confirm('Delete this permission?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-secondary py-5">No permissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $permissions->links() }}</div>
@endsection
