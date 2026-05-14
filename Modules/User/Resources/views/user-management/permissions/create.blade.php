@extends('layouts.authenticated')

@section('title', 'Create Permission | HMS')
@section('page-title', 'Create Permission')

@section('content')
    <form method="POST" action="{{ route('user-management.permissions.store') }}">
        @csrf
        @include('user::user-management.permissions.partials.form')
    </form>
@endsection
