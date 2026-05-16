@extends('layouts.authenticated')

@section('title', 'Edit Room Type | HMS')
@section('page-title', 'Edit Room Type')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">Edit Room Type</h1>
        <p class="text-secondary mb-0">{{ $hotel->name }} / {{ $roomType->name }}</p>
    </div>

    <form method="POST" action="{{ route('room-management.room-types.update', $roomType) }}">
        @csrf
        @method('PUT')
        @include('room::room-types.partials.form', ['roomType' => $roomType, 'rateTypes' => $rateTypes])
    </form>
@endsection
