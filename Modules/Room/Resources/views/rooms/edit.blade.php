@extends('layouts.authenticated')

@section('title', 'Edit Room | HMS')
@section('page-title', 'Edit Room')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">Edit Room</h1>
        <p class="text-secondary mb-0">{{ $hotel->name }} / Room {{ $room->room_number }}</p>
    </div>

    <form method="POST" action="{{ route('room-management.rooms.update', $room) }}">
        @csrf
        @method('PUT')
        @include('room::rooms.partials.form', ['room' => $room, 'roomTypes' => $roomTypes, 'statuses' => $statuses])
    </form>
@endsection
