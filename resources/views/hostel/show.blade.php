@extends('layouts.app')
@section('title', $hostel->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $hostel->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hostel.hostels.index') }}">Hostels</a></li>
            <li class="breadcrumb-item active">{{ $hostel->name }}</li>
        </ol></nav>
    </div>
    @can('manage-hostel')
    <div class="d-flex gap-2">
        <a href="{{ route('hostel.rooms.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i>Add Room</a>
        <a href="{{ route('hostel.hostels.edit', $hostel) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    </div>
    @endcan
</div>
<div class="row g-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="text-muted mb-3">Details</h6>
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">Type</dt><dd class="col-7"><span class="badge bg-secondary">{{ ucfirst($hostel->type) }}</span></dd>
                    <dt class="col-5 text-muted fw-normal">Location</dt><dd class="col-7">{{ $hostel->location ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Warden</dt><dd class="col-7">{{ optional(optional($hostel->warden)->user)->name ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Rooms</dt><dd class="col-7">{{ $hostel->rooms->count() }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><h6 class="mb-0">Rooms</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Room No.</th><th>Type</th><th>Capacity</th><th>Occupied</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            @forelse($hostel->rooms as $room)
                            <tr>
                                <td>{{ $room->room_number }}</td>
                                <td>{{ ucfirst($room->room_type) }}</td>
                                <td>{{ $room->capacity }}</td>
                                <td>{{ $room->activeAllocations()->count() }}</td>
                                <td><span class="badge bg-{{ $room->status === 'available' ? 'success' : ($room->status === 'full' ? 'danger' : 'warning text-dark') }}">{{ ucfirst($room->status) }}</span></td>
                                <td>
                                    <a href="{{ route('hostel.rooms.show', $room) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">No rooms added yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
