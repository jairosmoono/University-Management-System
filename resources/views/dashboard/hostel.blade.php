@extends('layouts.app')
@section('title', 'Hostel Dashboard')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1><i class="bi bi-house-door me-2" style="color:var(--secondary)"></i>Hostel Dashboard</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Dashboard</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('hostel.allocations.index') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i> Allocate Room
        </a>
        <a href="{{ route('hostel.hostels.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-building me-1"></i> Manage Hostels
        </a>
    </div>
</div>

{{-- ── STAT CARDS ─────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(11,31,58,0.1);color:#0B1F3A"><i class="bi bi-building"></i></div>
            <div class="stat-value">{{ $hostels->count() }}</div>
            <div class="stat-label text-muted">Hostels</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(13,110,253,0.1);color:#0d6efd"><i class="bi bi-door-open"></i></div>
            <div class="stat-value">{{ number_format($totalRooms) }}</div>
            <div class="stat-label text-muted">Total Rooms</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(108,117,125,0.1);color:#6c757d"><i class="bi bi-people"></i></div>
            <div class="stat-value">{{ number_format($totalCapacity) }}</div>
            <div class="stat-label text-muted">Total Capacity</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(220,53,69,0.1);color:#dc3545"><i class="bi bi-person-fill"></i></div>
            <div class="stat-value text-danger">{{ number_format($occupied) }}</div>
            <div class="stat-label text-muted">Occupied</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(25,135,84,0.1);color:#198754"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value text-success">{{ number_format($availableBeds) }}</div>
            <div class="stat-label text-muted">Available Beds</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            @php $rateColor = $occupancyRate >= 90 ? 'danger' : ($occupancyRate >= 70 ? 'warning' : 'success') @endphp
            <div class="stat-icon mx-auto mb-2" style="background:rgba(255,193,7,0.1);color:#ffc107"><i class="bi bi-bar-chart-fill"></i></div>
            <div class="stat-value text-{{ $rateColor }}">{{ $occupancyRate }}%</div>
            <div class="stat-label text-muted">Occupancy Rate</div>
        </div>
    </div>
</div>

{{-- ── ALERTS ──────────────────────────────────────────────────────────────── --}}
@if($overdueCheckouts->count() > 0)
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
    <div>
        <strong>{{ $overdueCheckouts->count() }} overdue checkout(s)</strong> — students have passed their expected vacate date.
        <a href="{{ route('hostel.allocations.index') }}" class="alert-link ms-1">View allocations</a>
    </div>
</div>
@endif
@if($upcomingCheckouts->count() > 0)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-clock-fill fs-5 flex-shrink-0"></i>
    <div>
        <strong>{{ $upcomingCheckouts->count() }} checkout(s)</strong> due within the next 7 days.
    </div>
</div>
@endif

{{-- ── HOSTEL OCCUPANCY TABLE + ROOM TYPES ─────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-building me-2 text-primary"></i>Hostel Occupancy</h6>
                <a href="{{ route('hostel.hostels.index') }}" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Hostel</th>
                                <th>Type</th>
                                <th class="text-center">Rooms</th>
                                <th class="text-center">Capacity</th>
                                <th class="text-center">Occupied</th>
                                <th class="text-center">Available</th>
                                <th style="min-width:120px">Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hostelStats as $row)
                            @php $gc = ['male'=>'primary','female'=>'danger','mixed'=>'success'] @endphp
                            <tr>
                                <td class="ps-3 fw-semibold">
                                    <a href="{{ route('hostel.rooms.index', ['hostel_id' => $row['hostel']->id]) }}" class="text-decoration-none">
                                        {{ $row['hostel']->name }}
                                    </a>
                                </td>
                                <td><span class="badge bg-{{ $gc[$row['hostel']->type] ?? 'secondary' }}">{{ ucfirst($row['hostel']->type) }}</span></td>
                                <td class="text-center">{{ $row['hostel']->rooms->count() }}</td>
                                <td class="text-center">{{ $row['capacity'] }}</td>
                                <td class="text-center text-danger fw-semibold">{{ $row['occupants'] }}</td>
                                <td class="text-center text-success fw-semibold">{{ $row['available'] }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:7px">
                                            <div class="progress-bar bg-{{ $row['rate'] >= 90 ? 'danger' : ($row['rate'] >= 70 ? 'warning' : 'success') }}"
                                                style="width:{{ $row['rate'] }}%"></div>
                                        </div>
                                        <small class="text-muted" style="width:32px">{{ $row['rate'] }}%</small>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No hostels found</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-door-open me-2 text-warning"></i>Rooms by Type</h6>
            </div>
            <div class="card-body">
                @forelse($roomsByType as $rt)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $rt->room_type) }}</div>
                        <small class="text-muted">{{ $rt->capacity }} beds total</small>
                    </div>
                    <span class="badge bg-primary rounded-pill fs-6">{{ $rt->count }}</span>
                </div>
                @empty
                <p class="text-muted text-center small mt-3">No room data available</p>
                @endforelse

                <hr>
                <h6 class="fw-semibold mb-3"><i class="bi bi-lightning me-1 text-warning"></i>Quick Actions</h6>
                <a href="{{ route('hostel.allocations.index') }}" class="btn btn-outline-primary w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-person-plus"></i> Allocate Room
                </a>
                <a href="{{ route('hostel.rooms.index') }}" class="btn btn-outline-secondary w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-door-open"></i> Manage Rooms
                </a>
                <a href="{{ route('hostel.allocations.occupancy') }}" class="btn btn-outline-info w-100 text-start d-flex align-items-center gap-2">
                    <i class="bi bi-graph-up"></i> Occupancy Report
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── RECENT ALLOCATIONS + UPCOMING CHECKOUTS ─────────────────────────────── --}}
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-person-check me-2 text-success"></i>Recent Allocations</h6>
                <a href="{{ route('hostel.allocations.index') }}" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-bottom">
                    @forelse($recentAllocations as $alloc)
                    <div class="list-group-item d-flex align-items-center gap-3 px-3 py-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:36px;height:36px;font-size:13px">
                            {{ strtoupper(substr(optional($alloc->student?->user)->name, 0, 1) ?? '?') }}
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold text-truncate">{{ optional($alloc->student?->user)->name ?? '—' }}</div>
                            <small class="text-muted">
                                {{ optional($alloc->hostelRoom?->hostel)->name ?? '—' }}
                                &mdash; Room {{ optional($alloc->hostelRoom)->room_number ?? '—' }}
                            </small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="badge bg-success">Active</span>
                            <div class="text-muted small mt-1">{{ \Carbon\Carbon::parse($alloc->allocation_date)->format('d M Y') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">No active allocations</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-calendar-x me-2 text-warning"></i>Upcoming Checkouts
                    @if($overdueCheckouts->count() > 0)
                    <span class="badge bg-danger ms-1">{{ $overdueCheckouts->count() }} overdue</span>
                    @endif
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-bottom">
                    @forelse($overdueCheckouts->concat($upcomingCheckouts)->take(10) as $alloc)
                    @php $overdue = $alloc->expected_vacate_date->lt(today()) @endphp
                    <div class="list-group-item d-flex align-items-center gap-3 px-3 py-2">
                        <div class="rounded-circle {{ $overdue ? 'bg-danger' : 'bg-warning' }} text-white d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:36px;height:36px;font-size:13px">
                            {{ strtoupper(substr(optional($alloc->student?->user)->name, 0, 1) ?? '?') }}
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold text-truncate">{{ optional($alloc->student?->user)->name ?? '—' }}</div>
                            <small class="text-muted">Room {{ optional($alloc->hostelRoom)->room_number ?? '—' }}</small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="badge bg-{{ $overdue ? 'danger' : 'warning' }} text-{{ $overdue ? 'white' : 'dark' }}">
                                {{ $overdue ? 'Overdue' : 'Due soon' }}
                            </span>
                            <div class="text-muted small mt-1">{{ $alloc->expected_vacate_date->format('d M Y') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check-circle text-success fs-4 d-block mb-1"></i>
                        No upcoming checkouts
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
