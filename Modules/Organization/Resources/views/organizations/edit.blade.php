@extends('layouts.authenticated')

@section('title', 'Edit Organization | HMS')
@section('page-title', 'Edit Organization')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">Edit Organization</h1>
        <p class="text-secondary mb-0">{{ $organization->name }}</p>
    </div>

    <form method="POST" action="{{ route('super-admin.organizations.update', $organization) }}">
        @csrf
        @method('PUT')
        @include('organization::organizations.partials.form', ['organization' => $organization])
    </form>
@endsection
