@extends('layouts.app')
@section('title', 'Students by Program Report')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Students by Program</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Students</li>
        </ol></nav>
    </div>
    <a href="{{ route('reports.export', 'students') }}?{{ http_build_query(request()->query()) }}"
        class="btn btn-danger btn-sm">
        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
    </a>
</div>

{{-- ── Filter Bar ──────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end" id="filterForm">
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1">Faculty</label>
                <select name="faculty_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Faculties</option>
                    @foreach($faculties as $f)
                        <option value="{{ $f->id }}" {{ request('faculty_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1">Program</label>
                <select name="program_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Programs</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" {{ request('program_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1">Gender</label>
                <select name="gender" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Genders</option>
                    <option value="male"   {{ request('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other"  {{ request('gender') === 'other'  ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label form-label-sm mb-1">Student Type</label>
                <select name="admission_type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="full-time" {{ request('admission_type') === 'full-time' ? 'selected' : '' }}>Full-Time</option>
                    <option value="part-time" {{ request('admission_type') === 'part-time' ? 'selected' : '' }}>Part-Time</option>
                    <option value="distance"  {{ request('admission_type') === 'distance'  ? 'selected' : '' }}>Distance</option>
                    <option value="online"    {{ request('admission_type') === 'online'    ? 'selected' : '' }}>Online</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label form-label-sm mb-1">Year</label>
                <select name="year_of_study" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All</option>
                    @for($y = 1; $y <= 6; $y++)
                        <option value="{{ $y }}" {{ request('year_of_study') == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label form-label-sm mb-1">Enrolled</label>
                <select name="enrollment_year" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Any Year</option>
                    @for($yr = date('Y'); $yr >= date('Y') - 8; $yr--)
                        <option value="{{ $yr }}" {{ request('enrollment_year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label form-label-sm mb-1">Status</label>
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="all"    {{ $status === 'all'    ? 'selected' : '' }}>All</option>
                    <option value="inactive"   {{ $status === 'inactive'   ? 'selected' : '' }}>Inactive</option>
                    <option value="graduated"  {{ $status === 'graduated'  ? 'selected' : '' }}>Graduated</option>
                    <option value="suspended"  {{ $status === 'suspended'  ? 'selected' : '' }}>Suspended</option>
                    <option value="dropped_out" {{ $status === 'dropped_out' ? 'selected' : '' }}>Dropped Out</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-funnel"></i>
                </button>
                <a href="{{ route('reports.students') }}" class="btn btn-sm btn-outline-secondary" title="Clear filters">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ── Summary Cards ────────────────────────────────────────────────── --}}
<div class="row g-3 mb-3">
    <div class="col-sm-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-2 fw-bold text-primary">{{ number_format($stats['totalFiltered']) }}</div>
                <div class="small text-muted text-uppercase">Total Students</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3 text-center">
                <div class="fs-2 fw-bold text-danger">{{ number_format($stats['totalDropouts']) }}</div>
                <div class="small text-muted text-uppercase">Dropped Out</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="small text-muted text-uppercase mb-2">By Gender</div>
                @foreach($stats['byGender'] as $g => $cnt)
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small">{{ ucfirst($g ?: 'Unknown') }}</span>
                    <span class="badge bg-{{ $g === 'male' ? 'primary' : ($g === 'female' ? 'danger' : 'secondary') }}">{{ $cnt }}</span>
                </div>
                @endforeach
                @if($stats['byGender']->isEmpty())<span class="text-muted small">—</span>@endif
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="small text-muted text-uppercase mb-2">By Year of Study</div>
                @foreach($stats['byYear'] as $yr => $cnt)
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small">Year {{ $yr }}</span>
                    <span class="badge bg-info text-dark">{{ $cnt }}</span>
                </div>
                @endforeach
                @if($stats['byYear']->isEmpty())<span class="text-muted small">—</span>@endif
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body py-3">
                <div class="small text-muted text-uppercase mb-2">By Student Type</div>
                @foreach($stats['byType'] as $t => $cnt)
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small">{{ ucfirst(str_replace('-', ' ', $t ?: 'Unknown')) }}</span>
                    <span class="badge bg-success">{{ $cnt }}</span>
                </div>
                @endforeach
                @if($stats['byType']->isEmpty())<span class="text-muted small">—</span>@endif
            </div>
        </div>
    </div>
</div>

{{-- ── Program Breakdown ────────────────────────────────────────────── --}}
@if($stats['programBreakdown']->isNotEmpty() && !request('program_id'))
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0"><i class="bi bi-bar-chart-line me-1 text-primary"></i> Enrollment by Program</h6>
        <span class="badge bg-secondary">{{ $stats['programBreakdown']->count() }} programs</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Program</th>
                        <th>Department</th>
                        <th>Level</th>
                        <th class="text-center">Students</th>
                        <th style="min-width:140px">Share</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['programBreakdown'] as $row)
                    @php
                        $pct = $stats['totalFiltered'] > 0 ? round($row['count'] / $stats['totalFiltered'] * 100, 1) : 0;
                        $prog = $row['program'];
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('reports.students') }}?{{ http_build_query(array_merge(request()->query(), ['program_id' => $prog?->id])) }}"
                                class="text-decoration-none fw-semibold">
                                {{ $prog?->name ?? '—' }}
                            </a>
                            @if($prog?->code)
                                <small class="text-muted ms-1">({{ $prog->code }})</small>
                            @endif
                        </td>
                        <td class="text-muted small">{{ optional($prog?->department)->name ?? '—' }}</td>
                        <td>
                            @if($prog?->level)
                                <span class="badge bg-light text-dark border">{{ ucfirst($prog->level) }}</span>
                            @else —
                            @endif
                        </td>
                        <td class="text-center fw-bold">{{ $row['count'] }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:6px">
                                    <div class="progress-bar bg-primary" style="width:{{ $pct }}%"></div>
                                </div>
                                <span class="small text-muted" style="min-width:36px">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- ── Student Listing ──────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0">
            <i class="bi bi-people me-1 text-primary"></i>
            Student List
            <span class="badge bg-secondary ms-1">{{ $students->total() }}</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Program</th>
                        <th>Department</th>
                        <th class="text-center">Year</th>
                        <th>Type</th>
                        <th>Enrolled</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $i => $student)
                    <tr>
                        <td class="text-muted small">{{ $students->firstItem() + $i }}</td>
                        <td><code class="small">{{ $student->student_id }}</code></td>
                        <td>{{ $student->full_name }}</td>
                        <td>
                            @if($student->gender)
                                <span class="badge bg-{{ $student->gender === 'male' ? 'primary' : ($student->gender === 'female' ? 'danger' : 'secondary') }} bg-opacity-75">
                                    {{ ucfirst($student->gender) }}
                                </span>
                            @else <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ optional($student->program)->name ?? '—' }}</td>
                        <td class="text-muted small">{{ optional($student->program?->department)->name ?? '—' }}</td>
                        <td class="text-center">{{ $student->year_of_study ? 'Y'.$student->year_of_study : '—' }}</td>
                        <td class="small">{{ ucfirst(str_replace('-', ' ', $student->admission_type ?? '—')) }}</td>
                        <td class="small">{{ $student->enrollment_date ? $student->enrollment_date->format('M Y') : '—' }}</td>
                        <td>
                            @php
                                $sc = match($student->status) {
                                    'active'    => 'success',
                                    'inactive'  => 'secondary',
                                    'graduated' => 'primary',
                                    'suspended' => 'warning',
                                    'dropped_out' => 'danger',
                                    default     => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $sc }}">{{ $student->status === 'dropped_out' ? 'Dropped Out' : ucfirst($student->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-5">
                        <i class="bi bi-search d-block fs-3 mb-2"></i> No students match the selected filters.
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $students->links() }}</div>

@endsection
