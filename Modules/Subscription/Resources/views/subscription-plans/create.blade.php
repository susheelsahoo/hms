@extends('layouts.authenticated')

@section('title', 'Create Subscription Plan | HMS')
@section('page-title', 'Create Subscription Plan')

@section('content')
    <div class="mb-4">
        <a href="{{ route('super-admin.subscription-plans.index') }}" class="text-decoration-none">
            <i class="bi bi-arrow-left"></i> Back to Plans
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <h1 class="h3 mb-4">Create New Subscription Plan</h1>

            <form method="POST" action="{{ route('super-admin.subscription-plans.store') }}">
                @csrf
                @include('subscription::subscription-plans.partials.form')
            </form>
        </div>
    </div>
@endsection
