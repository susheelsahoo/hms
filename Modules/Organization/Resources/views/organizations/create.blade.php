@extends('layouts.authenticated')

@section('title', 'Create Organization | HMS')
@section('page-title', 'Create Organization')

@section('content')
    <div class="mb-4">
        <h1 class="h4 mb-1">Create Organization</h1>
        <p class="text-secondary mb-0">Add a new tenant organization to HMS.</p>
    </div>

    <form method="POST" action="{{ route('super-admin.organizations.store') }}">
        @csrf
        @include('organization::organizations.partials.form', ['organization' => $organization])
    </form>
@endsection
