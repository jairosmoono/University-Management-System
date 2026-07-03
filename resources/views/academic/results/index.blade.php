@extends('layouts.app')
@section('title', 'Exam Results')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">
            @if($isLecturerView)
                <i class="bi bi-bar-chart-fill me-2 text-primary"></i>My Results
            @else
                Exam Results
            @endif
        </h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ $isLecturerView ? 'My Results' : 'Results' }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2">
        @if($isLecturerView)
            {{-- Lecturer: generate from grades + enter results dropdown --}}
            <form method="POST" action="{{ route('academic.results.generate-from-grades') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning"
                        onclick="return confirm('Auto-generate final results from your entered grades?')">
                    <i class="bi bi-lightning-charge me-1"></i> Generate from Grades
                </button>
            </form>
            @if($offerings->count())
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-pencil-square me-1"></i> Enter Results
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width:280px">
                    @foreach($offerings as $o)
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('academic.results.entry', $o) }}">
                            <div class="fw-semibold small">{{ optional($o->course)->code }} — {{ Str::limit(optional($o->course)->title ?? optional($o->course)->name ?? '', 35) }}</div>
                            <div class="text-muted" style="font-size:.75rem">{{ optional($o->semester)->name }}</div>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        @else
            @can('manage-results')
            <form method="POST" action="{{ route('academic.results.generate-from-grades') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning"
                        onclick="return confirm('Auto-generate final results from entered grades for all course offerings?')">
                    <i class="bi bi-lightning-charge me-1"></i> Generate from Grades
                </button>
            </form>
            <form method="POST" action="{{ route('academic.results.calculate-gpa') }}" class="d-inline">
                @csrf
                <input type="hidden" name="semester_id" value="{{ request('semester_id') }}">
                <button type="submit" class="btn btn-success"
                        onclick="return confirm('{{ request('semester_id') ? 'Recalculate GPA for the selected semester/term?' : 'Recalculate GPA for ALL semesters/terms?' }}')">
                    <i class="bi bi-calculator me-1"></i>
                    Calculate GPA{{ request('semester_id') ? ' (This Semester/Term)' : '' }}
                </button>
            </form>
            @endcan
        @endif
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<ul class="nav nav-tabs mb-4" id="resultsTabs">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#results-tab">Exam Results</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#gpa-tab">GPA Records</a>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="results-tab">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <form method="GET" class="row g-2">
                    <div class="col-md-3">
                        <select name="academic_year_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Academic Years</option>
                            @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ request('academic_year_id') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="semester_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Semesters/Terms</option>
                            @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>{{ $sem->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="offering_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Courses</option>
                            @foreach($offerings as $o)
                            <option value="{{ $o->id }}" {{ request('offering_id') == $o->id ? 'selected' : '' }}>{{ optional($o->course)->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ route('academic.results.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <table class="table datatable table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Student ID</th><th>Student Name</th><th>Course</th><th>CA (/40)</th><th>Exam (/60)</th><th>Total (/100)</th><th>Grade</th><th>Points</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                        <tr>
                            <td><code>{{ optional($result->student)->student_id }}</code></td>
                            <td>{{ optional(optional($result->student)->user)->name }}</td>
                            <td>{{ optional(optional($result->courseOffering)->course)->code }}</td>
                            <td>{{ $result->ca_score ?? '—' }}</td>
                            <td>{{ $result->exam_score ?? '—' }}</td>
                            <td class="fw-bold">{{ $result->total_score ?? '—' }}</td>
                            <td>
                                @php $gradeColors = ['A+'=>'success','A'=>'success','A-'=>'success','B+'=>'primary','B'=>'primary','B-'=>'primary','C+'=>'warning','C'=>'warning','C-'=>'warning','D'=>'danger','F'=>'danger'] @endphp
                                <span class="badge bg-{{ $gradeColors[$result->grade] ?? 'secondary' }}">{{ $result->grade ?? '—' }}</span>
                            </td>
                            <td>{{ $result->grade_points ?? '—' }}</td>
                            <td>
                                @if($result->status === 'pass')
                                    <span class="badge bg-success">Pass</span>
                                @elseif($result->status === 'fail')
                                    <span class="badge bg-danger">Fail</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td>
                                @can('manage-results')
                                    @if($result->status === 'pending')
                                    <form method="POST" action="{{ route('academic.results.approve', $result) }}" class="d-inline"
                                          onsubmit="return confirm('Approve this result?')">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-success" title="Approve">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="gpa-tab">

        {{-- Stats --}}
        @php $standingColors = ["Dean's List"=>'success','Good Standing'=>'primary','Probation'=>'warning','Academic Dismissal'=>'danger']; @endphp
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-primary bg-opacity-10 p-3"><i class="bi bi-people text-primary fs-4"></i></div>
                        <div>
                            <div class="fw-bold fs-4">{{ $gpaStats['total'] }}</div>
                            <div class="text-muted small">GPA Records</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-info bg-opacity-10 p-3"><i class="bi bi-bar-chart text-info fs-4"></i></div>
                        <div>
                            <div class="fw-bold fs-4">{{ number_format($gpaStats['avg_cgpa'], 2) }}</div>
                            <div class="text-muted small">Average CGPA</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-success bg-opacity-10 p-3"><i class="bi bi-trophy text-success fs-4"></i></div>
                        <div>
                            <div class="fw-bold fs-4">{{ $gpaStats['deans_list'] }}</div>
                            <div class="text-muted small">Dean's List</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-danger bg-opacity-10 p-3"><i class="bi bi-exclamation-triangle text-danger fs-4"></i></div>
                        <div>
                            <div class="fw-bold fs-4">{{ $gpaStats['probation'] }}</div>
                            <div class="text-muted small">At Risk</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-2">
                <form method="GET" class="row g-2" id="gpaFilterForm">
                    {{-- preserve results-tab filters --}}
                    @foreach(['academic_year_id','semester_id','offering_id'] as $rk)
                        @if(request($rk))<input type="hidden" name="{{ $rk }}" value="{{ request($rk) }}">@endif
                    @endforeach
                    <input type="hidden" name="_tab" value="gpa">
                    <div class="col-md-3">
                        <select name="gpa_semester_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Semesters/Terms</option>
                            @foreach($semesters as $s)
                            <option value="{{ $s->id }}" {{ request('gpa_semester_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="gpa_program_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Programs</option>
                            @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ request('gpa_program_id') == $p->id ? 'selected' : '' }}>{{ $p->code }} — {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="gpa_standing" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Standings</option>
                            @foreach(["Dean's List"=>'success','Good Standing'=>'primary','Probation'=>'warning','Academic Dismissal'=>'danger'] as $st => $cl)
                            <option value="{{ $st }}" {{ request('gpa_standing') == $st ? 'selected' : '' }}>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="gpa_search" class="form-control form-control-sm" placeholder="Student ID or name…" value="{{ request('gpa_search') }}">
                    </div>
                    <div class="col-md-1 d-flex gap-1">
                        <button class="btn btn-sm btn-primary">Go</button>
                        <a href="{{ route('academic.results.index') }}?_tab=gpa" class="btn btn-sm btn-outline-secondary">✕</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Program</th>
                            <th>Semester/Term</th>
                            <th class="text-center">Semester/Term GPA</th>
                            <th style="min-width:180px">CGPA</th>
                            <th class="text-center">Credits</th>
                            <th>Standing</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gpaRecords as $gpa)
                        @php
                            $cgpa       = (float) $gpa->cgpa;
                            $cgpaPct    = min(100, round($cgpa / 4.0 * 100));
                            $cgpaColor  = $cgpa >= 3.5 ? 'success' : ($cgpa >= 2.0 ? 'primary' : ($cgpa >= 1.0 ? 'warning' : 'danger'));
                            $standing   = $gpa->academic_standing ?? 'Good Standing';
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold" style="font-size:0.85rem">
                                    <a href="{{ route('students.show', $gpa->student_id) }}" class="text-decoration-none">
                                        {{ optional($gpa->student)->student_id }}
                                    </a>
                                </div>
                                <small class="text-muted">{{ optional(optional($gpa->student)->user)->name }}</small>
                            </td>
                            <td><small>{{ optional(optional($gpa->student)->program)->code ?? '—' }}</small></td>
                            <td>
                                <small class="fw-semibold">{{ optional($gpa->semester)->name ?? '—' }}</small><br>
                                <small class="text-muted">{{ optional($gpa->semester?->academicYear)->name ?? '' }}</small>
                            </td>
                            <td class="text-center fw-bold text-primary fs-6">{{ number_format($gpa->gpa, 2) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:6px">
                                        <div class="progress-bar bg-{{ $cgpaColor }}" style="width:{{ $cgpaPct }}%"></div>
                                    </div>
                                    <span class="fw-bold text-{{ $cgpaColor }}" style="min-width:2.5rem">{{ number_format($cgpa, 2) }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <small>
                                    {{ $gpa->credits_earned ?? 0 }}/{{ $gpa->total_credits_earned ?? 0 }}
                                    <span class="text-muted">cr</span>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-{{ $standingColors[$standing] ?? 'secondary' }}">{{ $standing }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('students.show', $gpa->student_id) }}" class="btn btn-sm btn-outline-primary" title="View Student">
                                        <i class="bi bi-person"></i>
                                    </a>
                                    @can('manage-results')
                                    @if($gpa->semester_id)
                                    <form method="POST" action="{{ route('academic.results.recalculate-student', [$gpa->student_id, $gpa->semester_id]) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-secondary" title="Recalculate GPA">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-bar-chart display-5 d-block mb-2"></i>
                            No GPA records found. Run <strong>Calculate GPA</strong> after approving results.
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($gpaRecords->hasPages())
            <div class="card-footer bg-transparent">
                {{ $gpaRecords->appends(request()->except('gpa_page'))->links() }}
            </div>
            @endif
        </div>
    </div>
</div>


@push('scripts')
<script>
// Restore active tab from URL param
(function() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('_tab') === 'gpa') {
        document.querySelector('a[href="#gpa-tab"]').click();
    }
})();

</script>
@endpush
@endsection
