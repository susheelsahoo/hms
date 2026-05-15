@extends('layouts.authenticated')

@section('title', 'My Hotels | HMS')
@section('page-title', 'My Hotels')

@section('content')
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">My Hotels</h1>
        </div>
    </div>

    @if ($hotels->isEmpty())
        <div class="alert alert-info" role="alert">
            <strong>No hotels found.</strong> You don't have access to any hotels yet.
        </div>
    @else
        <div class="row g-4">
            @foreach ($hotels as $hotel)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $hotel->name }}</h5>
                            <p class="card-text text-secondary small">{{ $hotel->slug }}</p>
                            
                            <div class="mb-3">
                                @if ($hotel->description)
                                    <p class="card-text small">{{ Str::limit($hotel->description, 100) }}</p>
                                @endif
                                
                                <div class="small text-muted">
                                    <div class="mb-1"><strong>Location:</strong> {{ $hotel->city }}, {{ $hotel->country }}</div>
                                    <div class="mb-1"><strong>Check-in:</strong> {{ $hotel->checkin_time }}</div>
                                    <div class="mb-1"><strong>Check-out:</strong> {{ $hotel->checkout_time }}</div>
                                    <div><strong>Status:</strong> <span class="badge bg-{{ $hotel->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($hotel->status) }}</span></div>
                                </div>
                            </div>

                            @if ($selectedHotel && $selectedHotel->id === $hotel->id)
                                <div class="alert alert-success py-2 px-3 mb-2">
                                    <small><i class="bi bi-check-circle"></i> Currently Selected</small>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white d-grid gap-2">
                            @if (!$selectedHotel || $selectedHotel->id !== $hotel->id)
                                <form method="POST" action="{{ route('hotels.select', $hotel) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-check"></i> Select Hotel
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-success btn-sm w-100" disabled>
                                    <i class="bi bi-check-circle"></i> Selected
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
