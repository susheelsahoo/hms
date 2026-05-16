@extends('layouts.authenticated')

@section('title', 'Edit Rate Type | HMS')
@section('page-title', 'Edit Rate Type')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">Edit Rate Type</h1>
        <p class="text-secondary mb-0">{{ $hotel->name }} / {{ $rateType->name }}</p>
    </div>

    <form method="POST" action="{{ route('room-management.rate-types.update', $rateType) }}">
        @csrf
        @method('PUT')
        @include('room::rate-types.partials.form', ['rateType' => $rateType])
    </form>
@endsection

