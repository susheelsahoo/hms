@extends('layouts.authenticated')

@section('title', 'Room Statuses | HMS')
@section('page-title', 'Room Statuses')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">Room Statuses</h1>
        <p class="text-secondary mb-0">{{ $hotel->name }}</p>
    </div>

    <div class="row g-3">
        @foreach ($statuses as $status => $label)
            <div class="col-xl col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">{{ $label }}</span>
                        <span class="badge text-bg-secondary">{{ ($rooms[$status] ?? collect())->count() }}</span>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse (($rooms[$status] ?? collect()) as $room)
                            <div class="list-group-item">
                                <div class="fw-semibold">Room {{ $room->room_number }}</div>
                                <div class="small text-secondary mb-2">{{ $room->roomType?->name ?? 'N/A' }}</div>
                                <form method="POST" action="{{ route('room-management.room-statuses.update', $room) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="input-group input-group-sm">
                                        <select name="status" class="form-select">
                                            @foreach ($statuses as $value => $statusLabel)
                                                <option value="{{ $value }}" @selected($room->status === $value)>{{ $statusLabel }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-outline-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                        @empty
                            <div class="list-group-item text-secondary small py-4">No rooms.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
