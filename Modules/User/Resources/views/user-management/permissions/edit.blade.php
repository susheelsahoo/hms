@extends('layouts.authenticated')

@section('title', 'Edit Permission | HMS')
@section('page-title', 'Edit Permission')

@section('content')
    <form method="POST" action="{{ route('user-management.permissions.update', $permission) }}">
        @csrf
        @method('PUT')
        @include('user::user-management.permissions.partials.form')
    </form>
@endsection
