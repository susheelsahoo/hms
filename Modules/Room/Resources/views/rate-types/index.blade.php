@extends('layouts.authenticated')

@section('title', 'Rate Types | HMS')
@section('page-title', 'Rate Types')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">Rate Types</h1>
            <p class="text-secondary mb-0">{{ $hotel->name }}</p>
        </div>
        <a href="{{ route('room-management.rate-types.create') }}" class="btn btn-primary align-self-md-start">Create Rate Type</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Base Rate</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rateTypes as $rateType)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $rateType->name }}</div>
                                <div class="small text-secondary">{{ $rateType->slug }}</div>
                            </td>
                            <td>{{ number_format((float) $rateType->base_rate, 2) }}</td>
                            <td class="text-end">
                                <a href="{{ route('room-management.rate-types.edit', $rateType) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('room-management.rate-types.destroy', $rateType) }}" class="d-inline" onsubmit="return confirm('Delete this rate type?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-secondary py-5">No rate types found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $rateTypes->links() }}
    </div>
@endsection

