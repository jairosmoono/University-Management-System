@extends('layouts.app')
@section('title', 'Room ' . $room->room_number)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Room {{ $room->room_number }}</h4>
        <p class="text-muted mb-0">{{ optional($room->hostel)->name }}</p>
    </div>
    <a href="{{ route('hostel.rooms.edit', $room) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted fw-normal">Type</dt><dd class="col-7">{{ ucfirst($room->type) }}</dd>
                    <dt class="col-5 text-muted fw-normal">Capacity</dt><dd class="col-7">{{ $room->capacity }}</dd>
                    <dt class="col-5 text-muted fw-normal">Occupied</dt><dd class="col-7">{{ $room->occupied ?? 0 }}</dd>
                    <dt class="col-5 text-muted fw-normal">Flat</dt><dd class="col-7">{{ $room->floor ?? '—' }}</dd>
                    <dt class="col-5 text-muted fw-normal">Status</dt><dd class="col-7"><span class="badge bg-{{ $room->status === 'available' ? 'success' : ($room->status === 'full' ? 'danger' : 'secondary') }}">{{ ucfirst($room->status) }}</span></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><h6 class="mb-0">Current Occupants</h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Student</th><th>ID</th><th>From</th><th>To</th></tr></thead>
                        <tbody>
                            @forelse($room->activeAllocations as $alloc)
                            <tr>
                                <td>{{ optional($alloc->student)->full_name }}</td>
                                <td><code>{{ optional($alloc->student)->student_number }}</code></td>
                                <td>{{ $alloc->start_date }}</td>
                                <td>{{ $alloc->end_date ?? 'Current' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Room is vacant.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
