@extends('layouts.app')

@section('title', 'HMS')

@section('body')
    <main class="min-vh-100 d-flex align-items-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="p-4 p-md-5 bg-white rounded-3 shadow-sm border">
                        <span class="badge text-bg-primary mb-3">Hotel Management SaaS</span>
                        <h1 class="display-6 fw-semibold mb-3">Enterprise HMS control center</h1>
                        <p class="lead text-secondary mb-4">
                            Manage organizations, hotels, rooms, bookings, payments, invoices, notifications, and audits from one modular Laravel platform.
                        </p>
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
