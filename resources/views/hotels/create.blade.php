@extends('layouts.authenticated')

@section('title', 'Create Hotel | HMS')
@section('page-title', 'Create Hotel')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">Create Hotel</h1>
        <p class="text-secondary mb-0">{{ $organization->name }}</p>
    </div>

    <form method="POST" action="{{ route('super-admin.organizations.hotels.store', $organization) }}">
        @csrf
        @include('hotels.partials.form', ['organization' => $organization, 'hotel' => $hotel])
    </form>
@endsection
