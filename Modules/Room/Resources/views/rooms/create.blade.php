@extends('layouts.authenticated')

@section('title', 'Create Room | HMS')
@section('page-title', 'Create Room')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">Create Room</h1>
        <p class="text-secondary mb-0">{{ $hotel->name }}</p>
    </div>

    <form method="POST" action="{{ route('room-management.rooms.store') }}">
        @csrf
        @include('room::rooms.partials.form', ['room' => $room, 'roomTypes' => $roomTypes, 'statuses' => $statuses])
    </form>
@endsection
