@extends('layouts.authenticated')

@section('title', 'Organizations | HMS')
@section('page-title', 'Organizations')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">Organization Management</h1>
            <p class="text-secondary mb-0">Create and manage SaaS tenant organizations.</p>
        </div>
        <a href="{{ route('super-admin.organizations.create') }}" class="btn btn-primary align-self-md-start">Create Organization</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Hotels</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($organizations as $organization)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $organization->name }}</div>
                                <div class="small text-secondary">{{ $organization->slug }}</div>
                            </td>
                            <td>{{ $organization->email ?: 'N/A' }}</td>
                            <td>{{ collect([$organization->city, $organization->state, $organization->country])->filter()->join(', ') ?: 'N/A' }}</td>
                            <td>{{ $organization->hotels_count }}</td>
                            <td>{{ $organization->users_count }}</td>
                            <td><span class="badge text-bg-{{ $organization->status === 'active' ? 'success' : 'secondary' }}">{{ $organization->status }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('super-admin.organizations.hotels.index', $organization) }}" class="btn btn-sm btn-outline-secondary">Hotels</a>
                                <a href="{{ route('super-admin.organizations.edit', $organization) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('super-admin.organizations.destroy', $organization) }}" class="d-inline" onsubmit="return confirm('Delete this organization?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">No organizations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $organizations->links() }}
    </div>
@endsection
