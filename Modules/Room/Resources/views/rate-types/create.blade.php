@extends('layouts.authenticated')

@section('title', 'Create Rate Type | HMS')
@section('page-title', 'Create Rate Type')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">Create Rate Type</h1>
        <p class="text-secondary mb-0">{{ $hotel->name }}</p>
    </div>

    <form method="POST" action="{{ route('room-management.rate-types.store') }}">
        @csrf
        @include('room::rate-types.partials.form', ['rateType' => $rateType])
    </form>
@endsection

