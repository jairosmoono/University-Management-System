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
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHostelModal">
        <i class="bi bi-plus-circle me-1"></i> Add Hostel
    </button>
    @endcan
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-primary fw-bold">{{ $stats['total_hostels'] }}</h4><small class="text-muted">Total Hostels</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-info fw-bold">{{ $stats['total_rooms'] }}</h4><small class="text-muted">Total Rooms</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-success fw-bold">{{ $stats['available'] }}</h4><small class="text-muted">Available Spaces</small></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><h4 class="text-warning fw-bold">{{ $stats['occupied'] }}</h4><small class="text-muted">Occupied</small></div></div>
</div>

<div class="row g-3">
    @foreach($hostels as $hostel)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-semibold">{{ $hostel->name }}</h6>
                @php $gc = ['male'=>'primary','female'=>'danger','mixed'=>'success'] @endphp
                <span class="badge bg-{{ $gc[$hostel->type] ?? 'secondary' }}">{{ ucfirst($hostel->type) }}</span>
            </div>
            <div class="card-body">
                @php
                    $totalCapacity = $hostel->rooms->sum('capacity') ?? 0;
                    $occupied = $hostel->rooms->sum('current_occupants') ?? 0;
                    $available = $totalCapacity - $occupied;
                    $pct = $totalCapacity > 0 ? round(($occupied / $totalCapacity) * 100) : 0;
                @endphp
                <div class="progress mb-3" style="height:10px">
                    <div class="progress-bar bg-{{ $pct > 90 ? 'danger' : ($pct > 70 ? 'warning' : 'success') }}" style="width:{{ $pct }}%"></div>
                </div>
                <div class="row text-center g-2">
                    <div class="col-4">
                        <div class="fw-bold text-primary">{{ $hostel->rooms?->count() ?? 0 }}</div>
                        <small class="text-muted">Rooms</small>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-success">{{ $available }}</div>
                        <small class="text-muted">Available</small>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold text-danger">{{ $occupied }}</div>
                        <small class="text-muted">Occupied</small>
                    </div>
                </div>
                <hr>
                <p class="small text-muted mb-0"><i class="bi bi-person me-1"></i>Warden: {{ optional($hostel->warden)->name ?? '—' }}</p>
                @if($hostel->location)
                <p class="small text-muted mb-0"><i class="bi bi-geo-alt me-1"></i>{{ $hostel->location }}</p>
                @endif
            </div>
            <div class="card-footer bg-transparent border-0 d-flex gap-2">
                <a href="{{ route('hostel.rooms.index', ['hostel_id' => $hostel->id]) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                    <i class="bi bi-door-open me-1"></i> View Rooms
                </a>
                @can('manage-hostel')
                <button class="btn btn-sm btn-outline-secondary" onclick="editHostel({{ $hostel->id }}, '{{ addslashes($hostel->name) }}')">
                    <i class="bi bi-pencil"></i>
                </button>
                @endcan
            </div>
        </div>
    </div>
    @endforeach
</div>

@can('manage-hostel')
<div class="modal fade" id="addHostelModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('hostel.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Hostel</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Hostel Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type *</label>
                        <select name="type" class="form-select" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="mixed">Mixed</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection
