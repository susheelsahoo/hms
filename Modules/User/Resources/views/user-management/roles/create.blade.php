@extends('layouts.authenticated')

@section('title', 'Create Role | HMS')
@section('page-title', 'Create Role')

@section('content')
    <form method="POST" action="{{ route('user-management.roles.store') }}">
        @csrf
        @include('user::user-management.roles.partials.form')
    </form>
@endsection
