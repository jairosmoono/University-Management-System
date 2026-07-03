@extends('layouts.app')
@section('title', 'Scholarship Awards Report')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Scholarship Awards Report</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Scholarship Awards</li>
        </ol></nav>
    </div>
    <a href="{{ route('reports.export', 'scholarships') }}?{{ http_build_query(request()->query()) }}"
       class="btn btn-danger btn-sm">
        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
    </a>
</div>

{{-- Filter Bar --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label form-label-sm mb-1">Scholarship</label>
                <select name="scholarship_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Scholarships</option>
                    @foreach($allScholarships as $s)
                        <option value="{{ $s->id }}" {{ request('scholarship_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1">Status</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1">From Date</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1">To Date</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date') }}">
            </div>
            <div class="col-auto d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-funnel"></i>
                </button>
                <a href="{{ route('reports.scholarships') }}" class="btn btn-sm btn-outline-secondary" title="Clear filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-2 fw-bold text-primary">{{ number_format($stats['totalAwards']) }}</div>
                <div class="small text-muted text-uppercase">Total Awards</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-2 fw-bold text-success">{{ number_format($stats['activeAwards']) }}</div>
                <div class="small text-muted text-uppercase">Active</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-2 fw-bold text-warning">{{ number_format($stats['suspendedAwards']) }}</div>
                <div class="small text-muted text-uppercase">Suspended</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-2 fw-bold text-info">{{ number_format($stats['uniqueStudents']) }}</div>
                <div class="small text-muted text-uppercase">Unique Students</div>
            </div>
        </div>
    </div>
</div>

{{-- Breakdown by Scholarship --}}
@if($stats['byScholarship']->isNotEmpty() && !request('scholarship_id'))
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0"><i class="bi bi-award me-1 text-primary"></i> Awards by Scholarship</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Scholarship</th>
                        <th>Type</th>
                        <th>Coverage</th>
                        <th class="text-center">Total Awards</th>
                        <th style="min-width:130px">Share</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allScholarships as $sch)
                    @php
                        $cnt = $stats['byScholarship']->get($sch->id, 0);
                        $pct = $stats['totalAwards'] > 0 ? round($cnt / $stats['totalAwards'] * 100, 1) : 0;
                    @endphp
                    @if($cnt > 0)
                    <tr>
                        <td>
                            <a href="{{ route('reports.scholarships') }}?{{ http_build_query(array_merge(request()->query(), ['scholarship_id' => $sch->id])) }}"
                               class="text-decoration-none fw-semibold">{{ $sch->name }}</a>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ ucfirst($sch->type ?? '—') }}</span></td>
                        <td class="small text-muted">
                            @if($sch->coverage_type)
                                {{ ucfirst(str_replace('_', ' ', $sch->coverage_type)) }}
                                @if($sch->coverage_value)
                                    — {{ $sch->coverage_value }}{{ str_contains(strtolower($sch->coverage_type ?? ''), 'percent') ? '%' : '' }}
                                @endif
                            @else —
                            @endif
                        </td>
                        <td class="text-center fw-bold">{{ $cnt }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:6px">
                                    <div class="progress-bar bg-primary" style="width:{{ $pct }}%"></div>
                                </div>
                                <span class="small text-muted" style="min-width:36px">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Awards Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0">
            <i class="bi bi-list-ul me-1 text-primary"></i>
            Award Records
            <span class="badge bg-secondary ms-1">{{ $awards->total() }}</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Program</th>
                        <th>Scholarship</th>
                        <th>Coverage</th>
                        <th>Award Date</th>
                        <th>Awarded By</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($awards as $i => $award)
                    <tr>
                        <td class="text-muted small">{{ $awards->firstItem() + $i }}</td>
                        <td><code class="small">{{ $award->student?->student_id ?? '—' }}</code></td>
                        <td class="fw-semibold">{{ $award->student?->user?->name ?? '—' }}</td>
                        <td class="text-muted small">{{ $award->student?->program?->name ?? '—' }}</td>
                        <td>{{ $award->scholarship?->name ?? '—' }}</td>
                        <td class="small text-muted">
                            @if($award->scholarship?->coverage_type)
                                {{ ucfirst(str_replace('_', ' ', $award->scholarship->coverage_type)) }}
                                @if($award->scholarship->coverage_value)
                                    — {{ $award->scholarship->coverage_value }}{{ str_contains(strtolower($award->scholarship->coverage_type), 'percent') ? '%' : '' }}
                                @endif
                            @else —
                            @endif
                        </td>
                        <td class="small">{{ $award->award_date?->format('d M Y') ?? '—' }}</td>
                        <td class="small text-muted">{{ $award->awardedBy?->name ?? '—' }}</td>
                        <td>
                            @if($award->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($award->status === 'suspended')
                                <span class="badge bg-warning text-dark">Suspended</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($award->status) }}</span>
                            @endif
                        </td>
                        <td class="small text-muted" style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $award->notes }}">
                            {{ $award->notes ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-5">
                        <i class="bi bi-search d-block fs-3 mb-2"></i> No awards match the selected filters.
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $awards->links() }}</div>

@endsection
