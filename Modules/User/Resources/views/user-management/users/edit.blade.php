@extends('layouts.authenticated')

@section('title', 'Edit User | HMS')
@section('page-title', 'Edit User')

@section('content')
    <form method="POST" action="{{ route('user-management.users.update', $user) }}">
        @csrf
        @method('PUT')
        @include('user::user-management.users.partials.form')
    </form>
@endsection
