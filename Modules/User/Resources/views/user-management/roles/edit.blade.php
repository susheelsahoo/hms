@extends('layouts.authenticated')

@section('title', 'Edit Role | HMS')
@section('page-title', 'Edit Role')

@section('content')
    <form method="POST" action="{{ route('user-management.roles.update', $role) }}">
        @csrf
        @method('PUT')
        @include('user::user-management.roles.partials.form')
    </form>
@endsection
