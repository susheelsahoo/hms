@extends('layouts.authenticated')

@section('title', 'Create User | HMS')
@section('page-title', 'Create User')

@section('content')
    <form method="POST" action="{{ route('user-management.users.store') }}">
        @csrf
        @include('user::user-management.users.partials.form')
    </form>
@endsection
