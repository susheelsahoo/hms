@extends('layouts.authenticated')

@section('title', 'Users | HMS')
@section('page-title', 'Users')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">User Management</h1>
            <p class="text-secondary mb-0">Create users, assign roles, and control account status.</p>
        </div>
        <a href="{{ route('user-management.users.create') }}" class="btn btn-primary align-self-md-start">Create User</a>
    </div>

    <form method="GET" action="{{ route('user-management.users.index') }}" class="row g-3 align-items-end mb-4">
        <div class="col-12 col-md-5 col-lg-4">
            <label class="form-label" for="organization_id">Organization</label>
            <select class="form-select" id="organization_id" name="organization_id" onchange="this.form.submit()">
                <option value="">All organizations</option>
                <option value="platform" @selected($selectedOrganizationId === 'platform')>Platform / No Organization</option>
                @foreach ($organizations as $organization)
                    <option value="{{ $organization->id }}" @selected((string) $selectedOrganizationId === (string) $organization->id)>
                        {{ $organization->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-auto">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
            @if (filled($selectedOrganizationId))
                <a href="{{ route('user-management.users.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Organization</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                <div class="small text-secondary">{{ $user->phone ?: 'N/A' }}</div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->organization?->name ?: 'Platform' }}</td>
                            <td>{{ $user->role?->name ?: 'N/A' }}</td>
                            <td><span class="badge text-bg-{{ $user->status === 'active' ? 'success' : 'secondary' }}">{{ $user->status }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('user-management.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('user-management.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" @disabled($user->is(auth()->user()))>Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links() }}</div>
@endsection
