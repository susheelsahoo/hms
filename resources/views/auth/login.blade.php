@extends('layouts.app')

@section('title', 'Login | HMS')

@section('body')
    <main class="min-vh-100 d-flex align-items-center py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <div class="mb-4">
                                <span class="badge text-bg-primary mb-3">Hotel Management SaaS</span>
                                <h1 class="h3 mb-1">Sign in</h1>
                                <p class="text-secondary mb-0">Access your HMS workspace.</p>
                            </div>

                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    Please check your email and password.
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login.store') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email', 'admin@hms.test') }}"
                                        class="form-control @error('email') is-invalid @enderror"
                                        autocomplete="email"
                                        required
                                        autofocus
                                    >
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input
                                        id="password"
                                        type="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        autocomplete="current-password"
                                        required
                                    >
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check mb-4">
                                    <input id="remember" type="checkbox" name="remember" class="form-check-input">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Login</button>
                            </form>

                            <div class="mt-4 small text-secondary">
                                <div>Local super admin:</div>
                                <code>admin@hms.test</code> / <code>password</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
