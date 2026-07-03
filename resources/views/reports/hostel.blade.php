@extends('layouts.app')
@section('title', 'Hostel Report')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-house-door me-2" style="color:var(--secondary)"></i>Hostel Report</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @hasrole('super-admin|registrar|finance-officer')
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
            @endhasrole
            <li class="breadcrumb-item active">Hostel</li>
        </ol></nav>
    </div>
    <a href="{{ route('reports.export', 'hostel') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
        class="btn btn-outline-danger btn-sm">
        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
    </a>
</div>

{{-- ── STAT CARDS ─────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-3 text-primary">{{ $hostels->count() }}</div>
            <small class="text-muted">Hostels</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-3 text-secondary">{{ number_format($totalRooms) }}</div>
            <small class="text-muted">Total Rooms</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            <div class="fw-bold fs-3 text-danger">{{ number_format($totalOccupied) }}</div>
            <small class="text-muted">Occupied Beds</small>
            @if($overdueCount > 0)
            <div><span class="badge bg-danger mt-1">{{ $overdueCount }} overdue</span></div>
            @endif
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm p-3 text-center">
            @php $rateColor = $occupancyRate >= 90 ? 'danger' : ($occupancyRate >= 70 ? 'warning' : 'success') @endphp
            <div class="fw-bold fs-3 text-{{ $rateColor }}">{{ $occupancyRate }}%</div>
            <small class="text-muted">Occupancy Rate</small>
            <div class="progress mt-2" style="height:6px">
                <div class="progress-bar bg-{{ $rateColor }}" style="width:{{ $occupancyRate }}%"></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- ── PER-HOSTEL BREAKDOWN ─────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-building me-2 text-primary"></i>Per-Hostel Occupancy</h6>
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
                                <th style="min-width:130px">Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $gc = ['male'=>'primary','female'=>'danger','mixed'=>'success'] @endphp
                            @forelse($hostelStats as $row)
                            <tr>
                                <td class="ps-3 fw-semibold">{{ $row['hostel']->name }}</td>
                                <td><span class="badge bg-{{ $gc[$row['hostel']->type] ?? 'secondary' }}">{{ ucfirst($row['hostel']->type) }}</span></td>
                                <td class="text-center">{{ $row['rooms'] }}</td>
                                <td class="text-center">{{ $row['capacity'] }}</td>
                                <td class="text-center text-danger fw-semibold">{{ $row['occupied'] }}</td>
                                <td class="text-center text-success fw-semibold">{{ $row['available'] }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height:7px">
                                            <div class="progress-bar bg-{{ $row['rate'] >= 90 ? 'danger' : ($row['rate'] >= 70 ? 'warning' : 'success') }}"
                                                style="width:{{ $row['rate'] }}%"></div>
                                        </div>
                                        <small class="text-muted" style="width:34px">{{ $row['rate'] }}%</small>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No hostel data</td></tr>
                            @endforelse
                            <tr class="table-light fw-semibold">
                                <td class="ps-3">Total</td>
                                <td></td>
                                <td class="text-center">{{ $hostelStats->sum('rooms') }}</td>
                                <td class="text-center">{{ $hostelStats->sum('capacity') }}</td>
                                <td class="text-center text-danger">{{ $hostelStats->sum('occupied') }}</td>
                                <td class="text-center text-success">{{ $hostelStats->sum('available') }}</td>
                                <td><small class="text-muted">{{ $occupancyRate }}% overall</small></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROOM TYPES + GENDER SPLIT ───────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-door-open me-2 text-warning"></i>Room Types</h6>
            </div>
            <div class="card-body">
                @forelse($roomsByType as $rt)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $rt->room_type) }}</span>
                        <small class="text-muted ms-1">({{ $rt->capacity }} beds)</small>
                    </div>
                    <span class="badge bg-primary rounded-pill">{{ $rt->rooms }} rooms</span>
                </div>
                @empty
                <p class="text-muted small mb-0">No room data</p>
                @endforelse
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-gender-ambiguous me-2 text-info"></i>By Gender</h6>
            </div>
            <div class="card-body">
                @foreach(['male' => 'primary', 'female' => 'danger', 'mixed' => 'success'] as $type => $color)
                @if(isset($byGender[$type]))
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <span class="badge bg-{{ $color }} me-1">{{ ucfirst($type) }}</span>
                        <small class="text-muted">{{ $byGender[$type]['count'] }} hostel(s)</small>
                    </div>
                    <small class="fw-semibold">{{ $byGender[$type]['capacity'] }} beds</small>
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ── MONTHLY ALLOCATION TREND ─────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-graph-up me-2 text-success"></i>Monthly Allocations (Last 12 Months)</h6>
    </div>
    <div class="card-body">
        <canvas id="trendChart" height="70"></canvas>
    </div>
</div>

{{-- ── ALLOCATION LIST ──────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header py-3 border-bottom">
        <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-list-ul me-2"></i>Allocation Records</h6>
    </div>

    {{-- Filters --}}
    <div class="card-body border-bottom py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Hostel</label>
                <select name="hostel_id" class="form-select form-select-sm">
                    <option value="">All Hostels</option>
                    @foreach($hostels as $h)
                    <option value="{{ $h->id }}" {{ request('hostel_id') == $h->id ? 'selected' : '' }}>{{ $h->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="active"  {{ request('status') === 'active'  ? 'selected' : '' }}>Active</option>
                    <option value="vacated" {{ request('status') === 'vacated' ? 'selected' : '' }}>Vacated</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('reports.hostel') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Student</th>
                        <th>Hostel</th>
                        <th>Room</th>
                        <th>Allocated</th>
                        <th>Expected Vacate</th>
                        <th>Actual Vacate</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allocations as $alloc)
                    <tr>
                        <td class="ps-3 text-muted small">{{ $allocations->firstItem() + $loop->index }}</td>
                        <td class="fw-semibold">{{ optional($alloc->student?->user)->name ?? '—' }}</td>
                        <td>{{ optional($alloc->hostelRoom?->hostel)->name ?? '—' }}</td>
                        <td>{{ optional($alloc->hostelRoom)->room_number ?? '—' }}</td>
                        <td>{{ $alloc->allocation_date?->format('d M Y') ?? '—' }}</td>
                        <td>
                            @if($alloc->expected_vacate_date)
                                @php $overdue = $alloc->status === 'active' && $alloc->expected_vacate_date->lt(today()) @endphp
                                <span class="{{ $overdue ? 'text-danger fw-semibold' : '' }}">
                                    {{ $alloc->expected_vacate_date->format('d M Y') }}
                                    @if($overdue) <i class="bi bi-exclamation-circle ms-1" title="Overdue"></i> @endif
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $alloc->actual_vacate_date?->format('d M Y') ?? '—' }}</td>
                        <td>
                            @if($alloc->status === 'active')
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Vacated</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No allocation records match the selected filters</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-3 py-2">
            {{ $allocations->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels = @json($monthlyTrend->pluck('month')->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->format('M Y')));
    const data   = @json($monthlyTrend->pluck('count'));

    new Chart(document.getElementById('trendChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'New Allocations',
                data,
                backgroundColor: 'rgba(32,201,151,0.7)',
                borderColor: 'rgba(32,201,151,1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
})();
</script>
@endpush
@endsection
