@extends('layouts.authenticated')

@section('title', 'Edit Hotel | HMS')
@section('page-title', 'Edit Hotel')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">Edit Hotel</h1>
        <p class="text-secondary mb-0">{{ $organization->name }} / {{ $hotel->name }}</p>
    </div>

    <form method="POST" action="{{ route('super-admin.organizations.hotels.update', [$organization, $hotel]) }}">
        @csrf
        @method('PUT')
        @include('hotel::hotels.partials.form', ['organization' => $organization, 'hotel' => $hotel])
    </form>
@endsection
