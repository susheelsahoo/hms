@extends('layouts.authenticated')

@section('title', 'Hotels | HMS')
@section('page-title', 'Hotels')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">Hotels</h1>
            <p class="text-secondary mb-0">{{ $organization->name }}</p>
        </div>
        <div class="d-flex gap-2 align-self-md-start">
            <a href="{{ route('super-admin.organizations.index') }}" class="btn btn-outline-secondary">Organizations</a>
            <a href="{{ route('super-admin.organizations.hotels.create', $organization) }}" class="btn btn-primary">Create Hotel</a>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Location</th>
                        <th>Currency</th>
                        <th>Check-in/out</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($hotels as $hotel)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $hotel->name }}</div>
                                <div class="small text-secondary">{{ $hotel->slug }}</div>
                            </td>
                            <td>{{ $hotel->email ?: 'N/A' }}</td>
                            <td>{{ collect([$hotel->city, $hotel->state, $hotel->country])->filter()->join(', ') ?: 'N/A' }}</td>
                            <td>{{ $hotel->currency }}</td>
                            <td>{{ $hotel->checkin_time ?: 'N/A' }} / {{ $hotel->checkout_time ?: 'N/A' }}</td>
                            <td><span class="badge text-bg-{{ $hotel->status === 'active' ? 'success' : 'secondary' }}">{{ $hotel->status }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('super-admin.organizations.hotels.edit', [$organization, $hotel]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('super-admin.organizations.hotels.destroy', [$organization, $hotel]) }}" class="d-inline" onsubmit="return confirm('Delete this hotel?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">No hotels found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $hotels->links() }}
    </div>
@endsection
