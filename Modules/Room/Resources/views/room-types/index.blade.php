@extends('layouts.authenticated')

@section('title', 'Room Types | HMS')
@section('page-title', 'Room Types')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">Room Types</h1>
            <p class="text-secondary mb-0">{{ $hotel->name }}</p>
        </div>
        <a href="{{ route('room-management.room-types.create') }}" class="btn btn-primary align-self-md-start">Create Room Type</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Rate Type</th>
                        <th>Capacity</th>
                        <th>Base Price</th>
                        <th>Bed / Size</th>
                        <th>Rooms</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roomTypes as $roomType)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $roomType->name }}</div>
                                <div class="small text-secondary">{{ $roomType->slug }}</div>
                            </td>
                            <td>{{ $roomType->rateType?->name ?? 'None' }}</td>
                            <td>{{ $roomType->max_adults }} adults, {{ $roomType->max_children }} children</td>
                            <td>{{ number_format((float) $roomType->base_price, 2) }}</td>
                            <td>{{ collect([$roomType->bed_type, $roomType->size])->filter()->join(' / ') ?: 'N/A' }}</td>
                            <td>{{ $roomType->rooms_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('room-management.room-types.edit', $roomType) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('room-management.room-types.destroy', $roomType) }}" class="d-inline" onsubmit="return confirm('Delete this room type?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">No room types found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $roomTypes->links() }}
    </div>
@endsection
