@extends('layouts.app')
@section('title', 'Hostel Occupancy')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-grid-3x3-gap me-2" style="color:var(--secondary)"></i>Hostel Occupancy Map</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('hostel.hostels.index') }}">Hostels</a></li>
            <li class="breadcrumb-item active">Occupancy</li>
        </ol></nav>
    </div>
    <a href="{{ route('hostel.allocations.occupancy.export') }}{{ request()->hostel_id ? '?hostel_id=' . request()->hostel_id : '' }}"
        class="btn btn-outline-danger btn-sm">
        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
    </a>
</div>

{{-- ── STATS + FILTER ROW ──────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4 align-items-center">
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-4 text-primary">{{ $hostels->count() }}</div>
            <small class="text-muted">Hostels</small>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-4 text-secondary">{{ $totalRooms }}</div>
            <small class="text-muted">Rooms</small>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-4 text-secondary">{{ number_format($totalCapacity) }}</div>
            <small class="text-muted">Total Beds</small>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-4 text-danger">{{ number_format($totalOccupied) }}</div>
            <small class="text-muted">Occupied</small>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-4 text-success">{{ number_format($totalCapacity - $totalOccupied) }}</div>
            <small class="text-muted">Available</small>
        </div>
    </div>
    <div class="col-6 col-md-2">
        @php $rateColor = $overallRate >= 90 ? 'danger' : ($overallRate >= 70 ? 'warning' : 'success') @endphp
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-4 text-{{ $rateColor }}">{{ $overallRate }}%</div>
            <small class="text-muted">Occupancy</small>
        </div>
    </div>
</div>

{{-- ── HOSTEL FILTER ───────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="d-flex align-items-center gap-3">
            <label class="form-label mb-0 text-muted small">Filter by hostel:</label>
            <select name="hostel_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <option value="">All Hostels</option>
                @foreach($allHostels as $h)
                <option value="{{ $h->id }}" {{ request('hostel_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                @endforeach
            </select>
            @if(request('hostel_id'))
            <a href="{{ route('hostel.allocations.occupancy') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            @endif
        </form>
    </div>
</div>

{{-- ── LEGEND ──────────────────────────────────────────────────────────────── --}}
<div class="d-flex flex-wrap gap-3 mb-4 align-items-center">
    <small class="text-muted fw-semibold me-1">Legend:</small>
    <span class="d-flex align-items-center gap-1"><span class="room-dot bg-success"></span> Empty</span>
    <span class="d-flex align-items-center gap-1"><span class="room-dot bg-warning"></span> Partially occupied</span>
    <span class="d-flex align-items-center gap-1"><span class="room-dot bg-danger"></span> Full</span>
    <span class="d-flex align-items-center gap-1"><span class="room-dot bg-secondary"></span> Maintenance</span>
</div>

{{-- ── PER-HOSTEL SECTIONS ─────────────────────────────────────────────────── --}}
@forelse($hostels as $hData)
@php
    $hostel = $hData['hostel'];
    $gc = ['male'=>'primary','female'=>'danger','mixed'=>'success'];
@endphp
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
        <div class="d-flex align-items-center gap-2">
            <h6 class="mb-0 fw-bold">{{ $hostel->name }}</h6>
            <span class="badge bg-{{ $gc[$hostel->type] ?? 'secondary' }}">{{ ucfirst($hostel->type) }}</span>
            @if($hostel->location)
            <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $hostel->location }}</small>
            @endif
        </div>
        <div class="d-flex align-items-center gap-3">
            <small class="text-muted">
                <span class="text-danger fw-semibold">{{ $hData['occupied'] }}</span>/{{ $hData['capacity'] }} occupied
                &nbsp;&bull;&nbsp;
                <span class="text-success fw-semibold">{{ $hData['available'] }}</span> available
            </small>
            @php $rc = $hData['rate'] >= 90 ? 'danger' : ($hData['rate'] >= 70 ? 'warning' : 'success') @endphp
            <div style="width:100px">
                <div class="d-flex justify-content-between mb-1"><small class="text-{{ $rc }}">{{ $hData['rate'] }}%</small></div>
                <div class="progress" style="height:6px">
                    <div class="progress-bar bg-{{ $rc }}" style="width:{{ $hData['rate'] }}%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @forelse($hData['floors'] as $floor => $rooms)
        <div class="mb-4">
            <h6 class="text-muted fw-semibold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-layers"></i>
                Flat {{ $floor ?? 'Ground' }}
                <span class="badge bg-light text-dark fw-normal ms-1">{{ $rooms->count() }} rooms</span>
            </h6>
            <div class="row g-2">
                @foreach($rooms as $room)
                @php
                    $occupants = $room->activeAllocations;
                    $occCount  = $occupants->count();
                    $isMaintenance = $room->status === 'maintenance';
                    $isFull    = $occCount >= $room->capacity;
                    $isEmpty   = $occCount === 0;
                    $cardColor = $isMaintenance ? 'secondary' : ($isFull ? 'danger' : ($isEmpty ? 'success' : 'warning'));
                    $bgClass   = $isMaintenance ? 'bg-light' : ($isFull ? 'bg-danger bg-opacity-10' : ($isEmpty ? 'bg-success bg-opacity-10' : 'bg-warning bg-opacity-10'));
                @endphp
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <div class="room-card {{ $bgClass }} border border-{{ $cardColor }} border-opacity-25 rounded p-2 h-100"
                        data-bs-toggle="tooltip"
                        title="{{ $room->room_number }}: {{ $occCount }}/{{ $room->capacity }} occupied"
                        style="cursor:pointer"
                        onclick="showRoom({{ $room->id }})">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-bold small">{{ $room->room_number }}</span>
                            <span class="badge bg-{{ $cardColor }} text-{{ $isMaintenance || $isFull ? 'white' : 'dark' }}" style="font-size:0.65rem">
                                @if($isMaintenance) <i class="bi bi-wrench"></i>
                                @else {{ $occCount }}/{{ $room->capacity }}
                                @endif
                            </span>
                        </div>
                        <div class="text-muted" style="font-size:0.7rem">{{ ucfirst($room->room_type) }}</div>
                        @if(!$isMaintenance)
                        <div class="progress mt-1" style="height:4px">
                            <div class="progress-bar bg-{{ $cardColor }}"
                                style="width:{{ $room->capacity > 0 ? round(($occCount / $room->capacity) * 100) : 0 }}%"></div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <p class="text-muted text-center py-3">No rooms configured for this hostel.</p>
        @endforelse
    </div>
</div>
@empty
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-building fs-1 d-block mb-2"></i>
        No hostels found.
    </div>
</div>
@endforelse

{{-- ── ROOM DETAIL MODAL ───────────────────────────────────────────────────── --}}
<div class="modal fade" id="roomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roomModalTitle">Room Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="roomModalBody">
                <div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></div>
            </div>
        </div>
    </div>
</div>

@php
$roomJson = [];
foreach ($hostels as $hData) {
    foreach ($hData['floors'] as $rooms) {
        foreach ($rooms as $room) {
            $roomJson[$room->id] = [
                'number'    => $room->room_number,
                'type'      => ucfirst($room->room_type),
                'floor'     => 'Flat ' . ($room->floor ?? 'Ground'),
                'capacity'  => $room->capacity,
                'status'    => $room->status,
                'amenities' => $room->amenities ?? [],
                'occupants' => $room->activeAllocations->map(fn($a) => [
                    'name'  => optional($a->student?->user)->name ?? '—',
                    'since' => optional($a->allocation_date)?->format('d M Y') ?? '—',
                    'vacate'=> optional($a->expected_vacate_date)?->format('d M Y') ?? '—',
                ])->values()->all(),
            ];
        }
    }
}
@endphp

@push('scripts')
<script>
const roomData = @json($roomJson);

function showRoom(id) {
    const r = roomData[id];
    if (!r) return;

    const isMaintenance = r.status === 'maintenance';
    const isFull = r.occupants.length >= r.capacity;
    const isEmpty = r.occupants.length === 0;
    const badgeColor = isMaintenance ? 'secondary' : (isFull ? 'danger' : (isEmpty ? 'success' : 'warning'));
    const badgeText  = isMaintenance ? 'Maintenance' : (isFull ? 'Full' : (isEmpty ? 'Available' : 'Partial'));

    document.getElementById('roomModalTitle').innerHTML =
        `Room ${r.number} <span class="badge bg-${badgeColor} ms-2">${badgeText}</span>`;

    let residents = '';
    if (r.occupants.length === 0) {
        residents = `<p class="text-muted text-center py-2"><i class="bi bi-check-circle text-success me-1"></i>Room is empty</p>`;
    } else {
        residents = r.occupants.map((o, i) => `
            <div class="d-flex align-items-center gap-3 py-2 ${i > 0 ? 'border-top' : ''}">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                    style="width:36px;height:36px;font-size:13px">${o.name.charAt(0).toUpperCase()}</div>
                <div>
                    <div class="fw-semibold">${o.name}</div>
                    <small class="text-muted">Since ${o.since}${o.vacate !== '—' ? ' &bull; Vacate: ' + o.vacate : ''}</small>
                </div>
            </div>`).join('');
    }

    const amenities = r.amenities.length
        ? r.amenities.map(a => `<span class="badge bg-light text-dark me-1">${a}</span>`).join('')
        : '<span class="text-muted small">None listed</span>';

    document.getElementById('roomModalBody').innerHTML = `
        <div class="row g-2 mb-3">
            <div class="col-4 text-center"><small class="text-muted d-block">Type</small><strong>${r.type}</strong></div>
            <div class="col-4 text-center"><small class="text-muted d-block">Flat</small><strong>${r.floor}</strong></div>
            <div class="col-4 text-center"><small class="text-muted d-block">Occupancy</small><strong>${r.occupants.length}/${r.capacity}</strong></div>
        </div>
        <div class="mb-3">
            <div class="progress" style="height:10px">
                <div class="progress-bar bg-${badgeColor}"
                    style="width:${r.capacity > 0 ? Math.round((r.occupants.length / r.capacity) * 100) : 0}%"></div>
            </div>
        </div>
        <div class="mb-3">
            <small class="text-muted fw-semibold d-block mb-1">Amenities</small>
            ${amenities}
        </div>
        <div>
            <small class="text-muted fw-semibold d-block mb-2">Current Residents (${r.occupants.length})</small>
            ${residents}
        </div>`;

    new bootstrap.Modal(document.getElementById('roomModal')).show();
}

// Init tooltips
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
});
</script>
@endpush

@push('styles')
<style>
.room-dot { display:inline-block; width:12px; height:12px; border-radius:50%; }
.room-card { transition: transform .15s, box-shadow .15s; }
.room-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }
</style>
@endpush
@endsection
