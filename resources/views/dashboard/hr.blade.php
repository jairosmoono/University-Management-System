@extends('layouts.app')
@section('title', 'HR Dashboard')

@section('content')
<div class="page-header d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1><i class="bi bi-people me-2" style="color:var(--secondary)"></i>HR Dashboard</h1>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Dashboard</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('hr.employees.index') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i> Add Employee
        </a>
        <a href="{{ route('hr.payroll.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-currency-dollar me-1"></i> Payroll
        </a>
    </div>
</div>

{{-- ── STAT CARDS ─────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(11,31,58,0.1);color:#0B1F3A"><i class="bi bi-people"></i></div>
            <div class="stat-value">{{ number_format($totalEmployees) }}</div>
            <div class="stat-label text-muted">Active Employees</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(255,193,7,0.15);color:#e6a817"><i class="bi bi-calendar-x"></i></div>
            <div class="stat-value {{ $pendingLeaves > 0 ? 'text-warning' : '' }}">{{ $pendingLeaves }}</div>
            <div class="stat-label text-muted">Pending Leaves</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(25,135,84,0.1);color:#198754"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value text-success">{{ $payrollProcessed }}</div>
            <div class="stat-label text-muted">Payslips Paid</div>
            <div class="stat-change text-muted mt-1">{{ now()->format('F') }}</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(220,53,69,0.1);color:#dc3545"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-value {{ $payrollPending > 0 ? 'text-danger' : '' }}">{{ $payrollPending }}</div>
            <div class="stat-label text-muted">Payroll Pending</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(13,110,253,0.1);color:#0d6efd"><i class="bi bi-briefcase"></i></div>
            <div class="stat-value">{{ $openListings }}</div>
            <div class="stat-label text-muted">Open Vacancies</div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="card stat-card p-3 text-center">
            <div class="stat-icon mx-auto mb-2" style="background:rgba(108,117,125,0.1);color:#6c757d"><i class="bi bi-cash-coin"></i></div>
            <div class="stat-value {{ $pendingAdvances > 0 ? 'text-warning' : '' }}">{{ $pendingAdvances }}</div>
            <div class="stat-label text-muted">Advance Requests</div>
        </div>
    </div>
</div>

{{-- ── PAYROLL NET TOTAL BANNER ─────────────────────────────────────────────── --}}
@if($payrollNetTotal > 0)
<div class="card border-0 shadow-sm mb-4" style="border-left:4px solid #198754 !important">
    <div class="card-body py-3 d-flex align-items-center justify-content-between">
        <div>
            <div class="text-muted small">Total Net Payroll — {{ now()->format('F Y') }}</div>
            <div class="fw-bold fs-4 text-success">{{ formatCurrency($payrollNetTotal) }}</div>
        </div>
        <a href="{{ route('hr.payroll.index') }}" class="btn btn-sm btn-outline-success">View Payroll</a>
    </div>
</div>
@endif

<div class="row g-3 mb-4">
    {{-- ── DEPARTMENT BREAKDOWN ──────────────────────────────────────────────── --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-diagram-3 me-2 text-primary"></i>Employees by Department</h6>
                <a href="{{ route('hr.employees.index') }}" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body">
                @php $maxCount = $byDepartment->max('active_count') ?: 1; @endphp
                @forelse($byDepartment as $dept)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold">{{ $dept->name }}</span>
                        <span class="small text-muted">{{ $dept->active_count }}</span>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar" style="width:{{ round(($dept->active_count / $maxCount) * 100) }}%;background:var(--primary)"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center small py-3">No department data available</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── EMPLOYMENT TYPE + QUICK ACTIONS ──────────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-pie-chart me-2 text-warning"></i>By Employment Type</h6>
            </div>
            <div class="card-body">
                @php
                    $typeColors = ['permanent'=>'success','contract'=>'primary','part-time'=>'warning','intern'=>'info'];
                @endphp
                @forelse($byType as $row)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-{{ $typeColors[$row->employment_type] ?? 'secondary' }}">
                        {{ ucfirst(str_replace('-', ' ', $row->employment_type)) }}
                    </span>
                    <span class="fw-semibold">{{ $row->count }}</span>
                </div>
                @empty
                <p class="text-muted small mb-0">No data</p>
                @endforelse
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-lightning me-2 text-warning"></i>Quick Actions</h6>
            </div>
            <div class="card-body p-3">
                <a href="{{ route('hr.leave.index') }}" class="btn btn-outline-warning w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-x"></i> Review Leave Requests
                    @if($pendingLeaves > 0)
                    <span class="badge bg-warning text-dark ms-auto">{{ $pendingLeaves }}</span>
                    @endif
                </a>
                <a href="{{ route('hr.salary-advances.index') }}" class="btn btn-outline-secondary w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-cash-coin"></i> Salary Advance Requests
                    @if($pendingAdvances > 0)
                    <span class="badge bg-secondary ms-auto">{{ $pendingAdvances }}</span>
                    @endif
                </a>
                <a href="{{ route('hr.payroll.index') }}" class="btn btn-outline-success w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-currency-dollar"></i> Run Payroll
                </a>
                <a href="{{ route('hr.employment-listings.index') }}" class="btn btn-outline-primary w-100 text-start d-flex align-items-center gap-2">
                    <i class="bi bi-briefcase"></i> Employment Listings
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- ── PAYROLL TREND CHART ───────────────────────────────────────────────── --}}
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-graph-up me-2 text-success"></i>Net Payroll Trend (Last 6 Months)</h6>
            </div>
            <div class="card-body">
                <canvas id="payrollChart" height="60"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- ── PENDING LEAVE REQUESTS ───────────────────────────────────────────── --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-calendar-x me-2 text-warning"></i>Pending Leave Requests</h6>
                <a href="{{ route('hr.leave.index') }}" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-bottom">
                    @forelse($recentLeaves as $leave)
                    <div class="list-group-item d-flex align-items-center gap-3 px-3 py-2">
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:36px;height:36px;font-size:13px">
                            {{ strtoupper(substr(optional($leave->employee?->user)->name, 0, 1) ?? '?') }}
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold text-truncate">{{ optional($leave->employee?->user)->name ?? '—' }}</div>
                            <small class="text-muted">
                                {{ optional($leave->leaveType)->name ?? 'Leave' }}
                                &bull; {{ $leave->start_date?->format('d M') }} – {{ $leave->end_date?->format('d M Y') }}
                                ({{ $leave->days_requested }} day{{ $leave->days_requested != 1 ? 's' : '' }})
                            </small>
                        </div>
                        <div class="d-flex gap-1 flex-shrink-0">
                            <form method="POST" action="{{ route('hr.leave.approve', $leave) }}">
                                @csrf
                                <button class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check"></i></button>
                            </form>
                            <button class="btn btn-sm btn-outline-danger" title="Reject"
                                onclick="rejectLeave({{ $leave->id }})"><i class="bi bi-x"></i></button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check-circle text-success fs-4 d-block mb-1"></i>
                        No pending leave requests
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ── EXPIRING CONTRACTS + RECENT HIRES ───────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-exclamation-triangle me-2 text-danger"></i>Contracts Expiring Soon
                    @if($expiringContracts->count() > 0)
                    <span class="badge bg-danger ms-1">{{ $expiringContracts->count() }}</span>
                    @endif
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-bottom">
                    @forelse($expiringContracts as $emp)
                    @php $daysLeft = today()->diffInDays($emp->contract_end_date) @endphp
                    <div class="list-group-item d-flex align-items-center gap-3 px-3 py-2">
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold text-truncate">{{ optional($emp->user)->name ?? '—' }}</div>
                            <small class="text-muted">{{ optional($emp->department)->name ?? '—' }} &bull; {{ $emp->designation }}</small>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="badge bg-{{ $daysLeft <= 7 ? 'danger' : 'warning' }} text-{{ $daysLeft <= 7 ? 'white' : 'dark' }}">
                                {{ $daysLeft }}d left
                            </span>
                            <div class="text-muted small mt-1">{{ $emp->contract_end_date->format('d M Y') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">
                        <i class="bi bi-check-circle text-success me-1"></i>No contracts expiring this month
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold"><i class="bi bi-person-plus me-2 text-success"></i>Recent Hires</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-bottom">
                    @forelse($recentHires as $emp)
                    <div class="list-group-item d-flex align-items-center gap-3 px-3 py-2">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:34px;height:34px;font-size:12px">
                            {{ strtoupper(substr(optional($emp->user)->name, 0, 1) ?? '?') }}
                        </div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold text-truncate small">{{ optional($emp->user)->name ?? '—' }}</div>
                            <small class="text-muted">{{ $emp->designation }} &bull; {{ $emp->join_date?->format('d M Y') }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3 small">No new hires in the last 30 days</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reject leave modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="POST" id="rejectForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header"><h6 class="modal-title">Reject Leave</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <textarea name="remarks" class="form-control" rows="3" placeholder="Reason for rejection..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
@php
    $months = $payrollTrend->map(fn($r) => \Carbon\Carbon::createFromDate($r->year, $r->month, 1)->format('M Y'));
    $totals = $payrollTrend->pluck('total');
@endphp
new Chart(document.getElementById('payrollChart'), {
    type: 'bar',
    data: {
        labels: @json($months),
        datasets: [{
            label: 'Net Pay',
            data: @json($totals),
            backgroundColor: 'rgba(25,135,84,0.7)',
            borderColor: 'rgba(25,135,84,1)',
            borderWidth: 1,
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' ZMW ' + Number(ctx.raw).toLocaleString('en', {minimumFractionDigits:2})
                }
            }
        },
        scales: {
            y: { beginAtZero: true, ticks: { callback: v => 'ZMW ' + Number(v).toLocaleString() } },
            x: { grid: { display: false } }
        }
    }
});

function rejectLeave(id) {
    document.getElementById('rejectForm').action = `/hr/leave/${id}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endpush
@endsection
