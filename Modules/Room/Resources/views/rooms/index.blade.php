@extends('layouts.authenticated')

@section('title', 'Rooms | HMS')
@section('page-title', 'Rooms')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">Rooms</h1>
            <p class="text-secondary mb-0">{{ $hotel->name }}</p>
        </div>
        <a href="{{ route('room-management.rooms.create') }}" class="btn btn-primary align-self-md-start">Create Room</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Room</th>
                        <th>Type</th>
                        <th>Floor</th>
                        <th>Capacity</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rooms as $room)
                        <tr>
                            <td class="fw-semibold">{{ $room->room_number }}</td>
                            <td>{{ $room->roomType?->name ?? 'N/A' }}</td>
                            <td>{{ $room->floor_number ?: 'N/A' }}</td>
                            <td>{{ $room->capacity }}</td>
                            <td>{{ $room->price !== null ? number_format((float) $room->price, 2) : 'Uses type price' }}</td>
                            <td><span class="badge text-bg-secondary">{{ ucfirst($room->status) }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('room-management.rooms.edit', $room) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('room-management.rooms.destroy', $room) }}" class="d-inline" onsubmit="return confirm('Delete this room?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">No rooms found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $rooms->links() }}
    </div>
@endsection
