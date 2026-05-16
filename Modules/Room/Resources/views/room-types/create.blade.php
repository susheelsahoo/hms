@extends('layouts.authenticated')

@section('title', 'Create Room Type | HMS')
@section('page-title', 'Create Room Type')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">Create Room Type</h1>
        <p class="text-secondary mb-0">{{ $hotel->name }}</p>
    </div>

    <form method="POST" action="{{ route('room-management.room-types.store') }}">
        @csrf
        @include('room::room-types.partials.form', ['roomType' => $roomType, 'rateTypes' => $rateTypes])
    </form>
@endsection
