@extends('layouts.app')
@section('title', 'Hostels')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Hostels</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Hostels</li>
        </ol></nav>
    </div>
    @can('manage-hostel')
    <a href="{{ route('hostel.hostels.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Add Hostel</a>
    @endcan
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3">
    @forelse($hostels as $hostel)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">{{ $hostel->name }}</h6>
                    <span class="badge bg-{{ $hostel->type === 'male' ? 'primary' : ($hostel->type === 'female' ? 'danger' : 'secondary') }}">{{ ucfirst($hostel->type) }}</span>
                </div>
                <p class="text-muted small mb-2">Warden: {{ optional(optional($hostel->warden)->user)->name ?? '—' }}</p>
                <p class="text-muted small mb-2">Location: {{ $hostel->location ?? '—' }}</p>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark">{{ $hostel->rooms_count }} rooms</span>
                </div>
            </div>
            <div class="card-footer bg-transparent d-flex gap-2">
                <a href="{{ route('hostel.hostels.show', $hostel) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                @can('manage-hostel')
                <a href="{{ route('hostel.hostels.edit', $hostel) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                <form action="{{ route('hostel.hostels.destroy', $hostel) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this hostel?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
                @endcan
            </div>
        </div>
    </div>
    @empty
    <div class="col-12"><div class="alert alert-info">No hostels found.</div></div>
    @endforelse
</div>
@endsection
