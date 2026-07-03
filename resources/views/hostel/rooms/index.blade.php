@extends('layouts.app')
@section('title', 'Hostel Rooms')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Hostel Rooms</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hostel.hostels.index') }}">Hostels</a></li>
            <li class="breadcrumb-item active">Rooms</li>
        </ol></nav>
    </div>
    @can('manage-hostel')
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
        <i class="bi bi-plus-circle me-1"></i> Add Room
    </button>
    @endcan
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="hostel_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Hostels</option>
                    @foreach($hostels as $hostel)
                    <option value="{{ $hostel->id }}" {{ request('hostel_id') == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="room_type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="single" {{ request('room_type') == 'single' ? 'selected' : '' }}>Single</option>
                    <option value="double" {{ request('room_type') == 'double' ? 'selected' : '' }}>Double</option>
                    <option value="triple" {{ request('room_type') == 'triple' ? 'selected' : '' }}>Triple</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="full" {{ request('status') == 'full' ? 'selected' : '' }}>Full</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table class="table datatable table-hover">
            <thead class="table-light">
                <tr>
                    <th>Room No.</th><th>Hostel</th><th>Flat</th><th>Type</th><th>Capacity</th><th>Occupied</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                <tr>
                    <td class="fw-semibold">{{ $room->room_number }}</td>
                    <td>{{ optional($room->hostel)->name }}</td>
                    <td>Flat {{ $room->floor ?? 1 }}</td>
                    <td>{{ ucfirst($room->room_type) }}</td>
                    <td>{{ $room->capacity }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:6px">
                                @php $occ = $room->capacity - ($room->available_beds ?? $room->capacity); $pct = $room->capacity > 0 ? round(($occ/$room->capacity)*100) : 0; @endphp
                                <div class="progress-bar bg-{{ $pct >= 100 ? 'danger' : ($pct >= 50 ? 'warning' : 'success') }}" style="width:{{ $pct }}%"></div>
                            </div>
                            <small>{{ $occ }}/{{ $room->capacity }}</small>
                        </div>
                    </td>
                    <td>
                        @php $sc = ['available'=>'success','full'=>'danger','maintenance'=>'warning','reserved'=>'info'] @endphp
                        <span class="badge bg-{{ $sc[$room->status] ?? 'secondary' }}">{{ ucfirst($room->status) }}</span>
                    </td>
                    <td>
                        <a href="{{ route('hostel.rooms.show', $room) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                        @can('manage-hostel')
                        <form method="POST" action="{{ route('hostel.rooms.destroy', $room) }}" class="d-inline" onsubmit="return confirm('Delete room?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@can('manage-hostel')
<div class="modal fade" id="addRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('hostel.rooms.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add Room</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Hostel *</label>
                            <select name="hostel_id" class="form-select" required>
                                <option value="">Select Hostel</option>
                                @foreach($hostels as $h)
                                <option value="{{ $h->id }}">{{ $h->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Room Number *</label>
                            <input type="text" name="room_number" class="form-control" placeholder="e.g. A101" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Flat</label>
                            <input type="number" name="floor" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Room Type *</label>
                            <select name="room_type" class="form-select" required>
                                <option value="single">Single (1 person)</option>
                                <option value="double">Double (2 persons)</option>
                                <option value="triple">Triple (3 persons)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Capacity *</label>
                            <input type="number" name="capacity" class="form-control" value="1" min="1" max="10" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Amenities</label>
                            <div class="d-flex gap-3">
                                @foreach(['WiFi','AC','Attached Bath','Study Desk','Wardrobe'] as $amenity)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="{{ $amenity }}" id="amenity_{{ Str::slug($amenity) }}">
                                    <label class="form-check-label" for="amenity_{{ Str::slug($amenity) }}">{{ $amenity }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Room</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endcan
@endsection
