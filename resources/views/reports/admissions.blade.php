@extends('layouts.app')
@section('title', 'Admissions Report')
@section('page-title', 'Admissions Report')

@section('content')

{{-- ── HEADER ─────────────────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-person-plus me-2 text-primary"></i>Admissions Report</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Admissions</li>
        </ol></nav>
    </div>
    <a href="{{ route('reports.export', 'admissions') }}?{{ http_build_query(request()->only(['academic_year_id','program_id','status','gender','from_date','to_date'])) }}"
       class="btn btn-danger btn-sm">
        <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
    </a>
</div>

{{-- ── STAT CARDS ──────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="stat-value text-primary">{{ number_format($totalAll) }}</div>
                    <div class="stat-label text-muted">Total Applications</div>
                    <small class="text-secondary">{{ $thisYear }} this year</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="stat-value text-success">{{ number_format($byStatus['approved'] ?? 0) }}</div>
                    <div class="stat-label text-muted">Approved</div>
                    @if($acceptanceRate !== null)
                    <small class="text-success fw-semibold">{{ $acceptanceRate }}% acceptance rate</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i></div>
                <div>
                    <div class="stat-value text-warning">{{ number_format($byStatus['pending'] ?? 0) }}</div>
                    <div class="stat-label text-muted">Pending</div>
                    @if(($byStatus['under_review'] ?? 0) > 0)
                    <small class="text-secondary">+ {{ $byStatus['under_review'] }} under review</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-mortarboard-fill"></i></div>
                <div>
                    <div class="stat-value text-info">{{ number_format($byStatus['enrolled'] ?? 0) }}</div>
                    <div class="stat-label text-muted">Enrolled</div>
                    <small class="text-danger">{{ $byStatus['rejected'] ?? 0 }} rejected</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── CHARTS ROW ──────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Monthly Trend --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-graph-up me-2 text-primary"></i>Monthly Application Trend (Last 12 Months)</h6>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="90"></canvas>
            </div>
        </div>
    </div>

    {{-- Status Distribution --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-pie-chart me-2 text-primary"></i>Status Distribution</h6>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <canvas id="statusChart" height="160"></canvas>
                <div class="mt-3 w-100">
                    @php
                        $statusColors = [
                            'pending'            => ['bg' => '#ffc107', 'label' => 'Pending'],
                            'under_review'       => ['bg' => '#17a2b8', 'label' => 'Under Review'],
                            'interview_scheduled'=> ['bg' => '#6f42c1', 'label' => 'Interview Scheduled'],
                            'approved'           => ['bg' => '#28a745', 'label' => 'Approved'],
                            'rejected'           => ['bg' => '#dc3545', 'label' => 'Rejected'],
                            'enrolled'           => ['bg' => '#0d6efd', 'label' => 'Enrolled'],
                        ];
                    @endphp
                    @foreach($byStatus as $status => $count)
                    @if($count > 0)
                    <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:0.8rem">
                        <span class="d-flex align-items-center gap-2">
                            <span style="width:10px;height:10px;border-radius:2px;background:{{ $statusColors[$status]['bg'] ?? '#aaa' }};display:inline-block"></span>
                            {{ $statusColors[$status]['label'] ?? ucwords(str_replace('_',' ',$status)) }}
                        </span>
                        <span class="fw-semibold">{{ number_format($count) }}</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── BY PROGRAM + GENDER ─────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Top Programs --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-bar-chart-steps me-2 text-success"></i>Applications by Program</h6>
            </div>
            <div class="card-body">
                @php $maxCount = $byProgram->max('count') ?: 1; @endphp
                @forelse($byProgram as $row)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold text-truncate" style="max-width:70%">
                            {{ optional($row->program)->name ?? 'Unknown Program' }}
                        </span>
                        <span class="small fw-bold text-primary">{{ number_format($row->count) }}</span>
                    </div>
                    <div class="progress" style="height:7px;border-radius:4px">
                        <div class="progress-bar bg-primary" style="width:{{ round($row->count / $maxCount * 100) }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3 mb-0">No data available.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Gender Breakdown --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-gender-ambiguous me-2 text-info"></i>Gender Breakdown</h6>
            </div>
            <div class="card-body">
                @php
                    $genderColors = ['male' => 'primary', 'female' => 'danger', 'other' => 'secondary'];
                    $genderIcons  = ['male' => 'gender-male', 'female' => 'gender-female', 'other' => 'person'];
                    $genderTotal  = $byGender->sum();
                @endphp
                @forelse($byGender as $gender => $count)
                @php $pct = $genderTotal > 0 ? round($count / $genderTotal * 100, 1) : 0; @endphp
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="stat-icon bg-{{ $genderColors[$gender] ?? 'secondary' }} bg-opacity-10 text-{{ $genderColors[$gender] ?? 'secondary' }}" style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-{{ $genderIcons[$gender] ?? 'person' }} fs-5"></i>
                    </div>
                    <div class="flex-1">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold text-capitalize">{{ $gender ?? 'Unknown' }}</span>
                            <span class="fw-bold text-{{ $genderColors[$gender] ?? 'secondary' }}">{{ number_format($count) }} <small class="text-muted fw-normal">({{ $pct }}%)</small></span>
                        </div>
                        <div class="progress" style="height:6px;border-radius:4px">
                            <div class="progress-bar bg-{{ $genderColors[$gender] ?? 'secondary' }}" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3 mb-0">No gender data available.</p>
                @endforelse

                {{-- Year over year --}}
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-center">
                        <div class="fw-bold fs-5 text-primary">{{ number_format($thisYear) }}</div>
                        <div class="text-muted small">This Year ({{ now()->year }})</div>
                    </div>
                    <div class="text-center text-muted">
                        @if($thisYear > $lastYear)
                        <span class="badge bg-success"><i class="bi bi-arrow-up"></i> {{ $lastYear > 0 ? round(($thisYear - $lastYear) / $lastYear * 100) . '%' : 'New' }}</span>
                        @elseif($thisYear < $lastYear)
                        <span class="badge bg-danger"><i class="bi bi-arrow-down"></i> {{ $lastYear > 0 ? round(($lastYear - $thisYear) / $lastYear * 100) . '%' : '' }}</span>
                        @else
                        <span class="badge bg-secondary">Unchanged</span>
                        @endif
                        <div class="text-muted" style="font-size:0.7rem">vs last year</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-5 text-secondary">{{ number_format($lastYear) }}</div>
                        <div class="text-muted small">Last Year ({{ now()->year - 1 }})</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── FILTERS ─────────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-2">
                <select name="academic_year_id" class="form-select form-select-sm">
                    <option value="">All Academic Years</option>
                    @foreach($academicYears as $ay)
                    <option value="{{ $ay->id }}" {{ request('academic_year_id') == $ay->id ? 'selected' : '' }}>
                        {{ $ay->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="semester_id" class="form-select form-select-sm">
                    <option value="">All Semesters/Terms</option>
                    @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                        {{ $sem->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="program_id" class="form-select form-select-sm">
                    <option value="">All Programs</option>
                    @foreach($programs as $prog)
                    <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                        {{ Str::limit($prog->name, 40) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach(['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','enrolled'=>'Enrolled'] as $val => $lbl)
                    <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <select name="gender" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="male"   {{ request('gender') == 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other"  {{ request('gender') == 'other'  ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="input-group input-group-sm">
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From">
                    <input type="date" name="to_date"   class="form-control" value="{{ request('to_date') }}"   placeholder="To">
                </div>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary px-3">Filter</button>
                @if(request()->hasAny(['academic_year_id','semester_id','program_id','status','gender','from_date','to_date']))
                <a href="{{ route('reports.admissions') }}" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── APPLICATIONS TABLE ──────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-2 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-table me-2 text-primary"></i>
            Applications
            @if($admissions->total() !== $totalAll)
            <span class="badge bg-primary ms-1">{{ number_format($admissions->total()) }} filtered</span>
            @endif
        </h6>
        <span class="text-muted small">{{ number_format($admissions->total()) }} record{{ $admissions->total() !== 1 ? 's' : '' }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="font-size:0.75rem">App. Number</th>
                        <th style="font-size:0.75rem">Applicant</th>
                        <th style="font-size:0.75rem">Program</th>
                        <th style="font-size:0.75rem">Semester/Term</th>
                        <th style="font-size:0.75rem">Gender</th>
                        <th style="font-size:0.75rem">Applied</th>
                        <th style="font-size:0.75rem">Status</th>
                        <th class="pe-3 text-end" style="font-size:0.75rem">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admissions as $admission)
                    @php
                        $sc = [
                            'pending'             => 'warning',
                            'under_review'        => 'info',
                            'interview_scheduled' => 'secondary',
                            'approved'            => 'success',
                            'rejected'            => 'danger',
                            'enrolled'            => 'primary',
                        ];
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <code style="font-size:0.78rem">{{ $admission->application_number }}</code>
                        </td>
                        <td>
                            <div class="fw-semibold" style="font-size:0.875rem">
                                {{ $admission->first_name }} {{ $admission->last_name }}
                            </div>
                            <small class="text-muted">{{ $admission->email }}</small>
                        </td>
                        <td>
                            <span style="font-size:0.8rem">{{ optional($admission->program)->name ?? '—' }}</span>
                            @if(optional(optional($admission->program)->department)->name)
                            <br><small class="text-muted">{{ $admission->program->department->name }}</small>
                            @endif
                        </td>
                        <td><small class="text-muted">{{ optional($admission->semester)->name ?? '—' }}</small></td>
                        <td><span class="text-capitalize small">{{ $admission->gender ?? '—' }}</span></td>
                        <td>
                            <small>{{ $admission->created_at->format('d M Y') }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{ $sc[$admission->status] ?? 'secondary' }}">
                                {{ ucwords(str_replace('_', ' ', $admission->status)) }}
                            </span>
                        </td>
                        <td class="pe-3 text-end">
                            <a href="{{ route('admissions.show', $admission) }}"
                               class="btn btn-sm btn-outline-primary" title="View Application">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            No applications match the selected filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($admissions->hasPages())
    <div class="card-footer bg-transparent">
        {{ $admissions->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Monthly Trend Chart ─────────────────────────────────────────────
    const trendData = @json($monthlyTrend);
    const labels    = trendData.map(r => {
        const [y, m] = r.month.split('-');
        return new Date(y, m - 1).toLocaleString('default', { month: 'short', year: '2-digit' });
    });

    new Chart(document.getElementById('trendChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Total',
                    data: trendData.map(r => r.total),
                    backgroundColor: 'rgba(13,110,253,0.15)',
                    borderColor: 'rgba(13,110,253,0.8)',
                    borderWidth: 2,
                    type: 'line',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    order: 1,
                },
                {
                    label: 'Approved',
                    data: trendData.map(r => r.approved),
                    backgroundColor: 'rgba(40,167,69,0.7)',
                    borderColor: '#28a745',
                    borderWidth: 1,
                    borderRadius: 4,
                    order: 2,
                },
                {
                    label: 'Rejected',
                    data: trendData.map(r => r.rejected),
                    backgroundColor: 'rgba(220,53,69,0.65)',
                    borderColor: '#dc3545',
                    borderWidth: 1,
                    borderRadius: 4,
                    order: 3,
                },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
                x: { grid: { display: false } },
            },
        },
    });

    // ── Status Doughnut Chart ───────────────────────────────────────────
    const statusData = @json($byStatus);
    const statusColors = {
        pending:             '#ffc107',
        under_review:        '#17a2b8',
        interview_scheduled: '#6f42c1',
        approved:            '#28a745',
        rejected:            '#dc3545',
        enrolled:            '#0d6efd',
    };
    const statusLabels = {
        pending:             'Pending',
        under_review:        'Under Review',
        interview_scheduled: 'Interview Scheduled',
        approved:            'Approved',
        rejected:            'Rejected',
        enrolled:            'Enrolled',
    };

    const keys   = Object.keys(statusData).filter(k => statusData[k] > 0);
    const values = keys.map(k => statusData[k]);
    const colors = keys.map(k => statusColors[k] || '#aaa');
    const slabels = keys.map(k => statusLabels[k] || k);

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: { labels: slabels, datasets: [{ data: values, backgroundColor: colors, borderWidth: 2 }] },
        options: {
            cutout: '65%',
            responsive: true,
            plugins: { legend: { display: false } },
        },
    });

});
</script>
@endpush

@endsection
