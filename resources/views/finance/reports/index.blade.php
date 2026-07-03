@extends('layouts.app')
@section('title', 'Finance Reports')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Finance Reports</h4>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
        <li class="breadcrumb-item active">Finance</li>
    </ol></nav>
</div>

<!-- Revenue Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-cash"></i></div>
                <div>
                    <div class="stat-value">K {{ number_format($summary['total_collected'] ?? 0, 2) }}</div>
                    <div class="stat-label text-muted">Total Collected</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-circle"></i></div>
                <div>
                    <div class="stat-value">K {{ number_format($summary['total_outstanding'] ?? 0, 2) }}</div>
                    <div class="stat-label text-muted">Outstanding</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-receipt"></i></div>
                <div>
                    <div class="stat-value">{{ $summary['total_bills'] ?? 0 }}</div>
                    <div class="stat-label text-muted">Total Bills</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-percent"></i></div>
                <div>
                    <div class="stat-value">{{ $summary['collection_rate'] ?? 0 }}%</div>
                    <div class="stat-label text-muted">Collection Rate</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Cards -->
<div class="row g-4">
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-graph-up-arrow"></i></div>
                    <h6 class="mb-0 fw-semibold">Revenue Collection Report</h6>
                </div>
                <p class="text-muted small">Detailed breakdown of payments received by period, method, and program.</p>
                <form method="GET" action="{{ route('finance.reports.revenue') }}">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <select name="academic_year_id" class="form-select form-select-sm">
                                <option value="">All Years</option>
                                @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <select name="semester_id" class="form-select form-select-sm">
                                <option value="">All Semesters/Terms</option>
                                @foreach($semesters as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-outline-primary flex-fill">View Report</button>
                        <button type="submit" name="export" value="pdf" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-pdf"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                    <h6 class="mb-0 fw-semibold">Outstanding Balances Report</h6>
                </div>
                <p class="text-muted small">List of students with unpaid or partial fee balances.</p>
                <form method="GET" action="{{ route('finance.reports.outstanding') }}">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <select name="academic_year_id" class="form-select form-select-sm">
                                <option value="">All Years</option>
                                @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <select name="program_id" class="form-select form-select-sm">
                                <option value="">All Programs</option>
                                @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->code }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-outline-primary flex-fill">View Report</button>
                        <button type="submit" name="export" value="pdf" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-pdf"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-pie-chart"></i></div>
                    <h6 class="mb-0 fw-semibold">Revenue by Program</h6>
                </div>
                <p class="text-muted small">Fee income breakdown per academic program and department.</p>
                <canvas id="revenueByProgram" height="160"></canvas>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">Monthly Collection Trend</h6>
                <small class="text-muted">Current Academic Year</small>
            </div>
            <div class="card-body">
                <canvas id="monthlyTrend" height="80"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const programData = @json($programRevenue ?? []);
    const monthlyData = @json($monthlyTrend ?? []);

    if (document.getElementById('revenueByProgram')) {
        new Chart(document.getElementById('revenueByProgram'), {
            type: 'doughnut',
            data: {
                labels: programData.map(p => p.code),
                datasets: [{ data: programData.map(p => p.total), backgroundColor: ['#0B1F3A','#8B0000','#1a3a6b','#c0392b','#2c5f8a','#d4a017'] }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }

    if (document.getElementById('monthlyTrend')) {
        new Chart(document.getElementById('monthlyTrend'), {
            type: 'bar',
            data: {
                labels: monthlyData.map(m => m.month),
                datasets: [{
                    label: 'Amount Collected (K)',
                    data: monthlyData.map(m => m.amount),
                    backgroundColor: 'rgba(11,31,58,0.7)',
                    borderColor: '#0B1F3A',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, ticks: { callback: v => 'K ' + v.toLocaleString() } } },
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>
@endpush
