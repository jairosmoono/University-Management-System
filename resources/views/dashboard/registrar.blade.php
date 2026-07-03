@extends('layouts.app')
@section('title', "Registrar's Dashboard")
@section('page-title', "Registrar's Office")

@section('content')

{{-- ── HEADER ─────────────────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1 fw-bold">
            <i class="bi bi-building-gear me-2 text-primary"></i>
            Registrar's Office
        </h4>
        @if($currentSemester || $currentAcademicYear)
        <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
            @if($currentAcademicYear)
            <span class="badge bg-primary px-3 py-2">
                <i class="bi bi-calendar3 me-1"></i>{{ $currentAcademicYear->name }}
            </span>
            @endif
            @if($currentSemester)
            <span class="badge bg-secondary px-3 py-2">
                <i class="bi bi-calendar-week me-1"></i>{{ $currentSemester->name }}
            </span>
            @endif
        </div>
        @endif
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admissions.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i> New Admission
        </a>
        <a href="{{ route('academic.registrations.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil-square me-1"></i> Registrations
        </a>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-graph-up me-1"></i> Reports
        </a>
    </div>
</div>

{{-- ── STAT CARDS ──────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('students.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-person-badge-fill"></i></div>
                <div>
                    <div class="stat-value text-primary">{{ number_format($totalActiveStudents) }}</div>
                    <div class="stat-label text-muted">Active Students</div>
                </div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('admissions.index') }}?status=pending" class="text-decoration-none">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-person-plus-fill"></i></div>
                <div>
                    <div class="stat-value text-warning">{{ number_format($pendingAdmissions) }}</div>
                    <div class="stat-label text-muted">
                        Pending Admissions
                        <span class="d-block text-secondary fw-normal" style="font-size:0.72rem">{{ $newAdmissionsThisYear }} this year</span>
                    </div>
                </div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('academic.registrations.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-pencil-square"></i></div>
                <div>
                    <div class="stat-value text-success">{{ number_format($activeRegistrations) }}</div>
                    <div class="stat-label text-muted">
                        Active Registrations
                        @if($currentSemester)
                        <span class="d-block text-secondary fw-normal" style="font-size:0.72rem">{{ $totalOfferings }} offerings</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('academic.examinations.index') }}" class="text-decoration-none">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-file-earmark-text-fill"></i></div>
                <div>
                    <div class="stat-value text-info">{{ $upcomingExams->count() }}</div>
                    <div class="stat-label text-muted">
                        Upcoming Exams
                        <span class="d-block text-secondary fw-normal" style="font-size:0.72rem">next 14 days</span>
                    </div>
                </div>
            </div>
        </div>
        </a>
    </div>
</div>

{{-- ── REGISTRATION STATUS SUMMARY ─────────────────────────────────────────── --}}
@if($regsByStatus->isNotEmpty())
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent py-2 d-flex align-items-center justify-content-between">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-bar-chart-steps me-2 text-primary"></i>Registration Status — Current Semester/Term</h6>
        <a href="{{ route('academic.registrations.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body py-3">
        @php
            $statusColors = ['registered' => 'success', 'approved' => 'primary', 'pending' => 'warning', 'dropped' => 'danger', 'completed' => 'info'];
            $total = $regsByStatus->sum();
        @endphp
        <div class="row g-3">
            @foreach($regsByStatus as $status => $count)
            <div class="col-6 col-md-3 col-lg-2">
                <div class="text-center p-3 rounded-3 bg-{{ $statusColors[$status] ?? 'secondary' }} bg-opacity-10">
                    <div class="fs-4 fw-bold text-{{ $statusColors[$status] ?? 'secondary' }}">{{ number_format($count) }}</div>
                    <div class="small text-muted">{{ ucfirst($status) }}</div>
                    @if($total > 0)
                    <div class="text-muted" style="font-size:0.72rem">{{ round($count / $total * 100) }}%</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="row g-3 mb-4">
    {{-- ── RECENT ADMISSIONS ──────────────────────────────────────────────── --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent py-2 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-person-plus me-2 text-warning"></i>Recent Admissions</h6>
                <a href="{{ route('admissions.index') }}" class="btn btn-sm btn-outline-warning">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="font-size:0.75rem">Applicant</th>
                                <th style="font-size:0.75rem">Program</th>
                                <th style="font-size:0.75rem">Status</th>
                                <th style="font-size:0.75rem" class="pe-3">Applied</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAdmissions as $admission)
                            @php $sc = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','enrolled'=>'primary']; @endphp
                            <tr>
                                <td class="ps-3">
                                    <span class="fw-semibold" style="font-size:0.875rem">{{ $admission->first_name }} {{ $admission->last_name }}</span>
                                </td>
                                <td><small class="text-muted">{{ optional($admission->program)->name ?? '—' }}</small></td>
                                <td>
                                    <span class="badge bg-{{ $sc[$admission->status] ?? 'secondary' }}">
                                        {{ ucfirst($admission->status) }}
                                    </span>
                                </td>
                                <td class="pe-3"><small class="text-muted">{{ $admission->created_at->format('d M') }}</small></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No admissions yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PROGRAM ENROLLMENT ─────────────────────────────────────────────── --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent py-2 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-mortarboard me-2 text-success"></i>Enrollment by Program</h6>
                <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-success">All Students</a>
            </div>
            <div class="card-body">
                @php $maxStudents = $programStats->max('active_students') ?: 1; @endphp
                @forelse($programStats as $prog)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-semibold text-truncate" style="max-width:70%">{{ $prog->name }}</span>
                        <span class="small fw-bold text-primary">{{ number_format($prog->active_students) }}</span>
                    </div>
                    <div class="progress" style="height:6px;border-radius:4px">
                        <div class="progress-bar bg-primary" style="width:{{ round(($prog->active_students / $maxStudents) * 100) }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3 mb-0">No program data available.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- ── PENDING RESULT ENTRY ───────────────────────────────────────────── --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent py-2 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-hourglass-split me-2 text-danger"></i>
                    Awaiting Result Entry
                    @if($offeringsWithNoResults->count() > 0)
                    <span class="badge bg-danger ms-1">{{ $offeringsWithNoResults->count() }}</span>
                    @endif
                </h6>
                <a href="{{ route('academic.results.index') }}" class="btn btn-sm btn-outline-danger">Results</a>
            </div>
            <div class="card-body p-0">
                @forelse($offeringsWithNoResults as $offering)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <div class="flex-1">
                        <div class="fw-semibold" style="font-size:0.875rem">
                            {{ optional($offering->course)->name ?? '—' }}
                        </div>
                        <small class="text-muted">
                            <code>{{ optional($offering->course)->code }}</code>
                            @if($offering->lecturer)
                            &bull; {{ optional($offering->lecturer->user)->name }}
                            @endif
                        </small>
                    </div>
                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">No Results</span>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-check-circle-fill fs-3 text-success d-block mb-2 opacity-75"></i>
                    All offerings have results entered.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── UPCOMING EXAMS ─────────────────────────────────────────────────── --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent py-2 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-file-earmark-text me-2 text-info"></i>Upcoming Examinations</h6>
                <a href="{{ route('academic.examinations.index') }}" class="btn btn-sm btn-outline-info">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($upcomingExams as $exam)
                @php
                    $daysLeft = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($exam->exam_date)->startOfDay(), false);
                    $urgency  = $daysLeft === 0 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'info');
                @endphp
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <div class="text-center flex-shrink-0" style="width:44px">
                        <div class="fw-bold text-{{ $urgency }}" style="font-size:1rem;line-height:1">
                            {{ \Carbon\Carbon::parse($exam->exam_date)->format('d') }}
                        </div>
                        <div class="text-muted" style="font-size:0.65rem;text-transform:uppercase">
                            {{ \Carbon\Carbon::parse($exam->exam_date)->format('M') }}
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="fw-semibold" style="font-size:0.875rem">
                            {{ optional(optional($exam->courseOffering)->course)->name ?? '—' }}
                        </div>
                        <small class="text-muted">
                            {{ ucfirst(str_replace('_', ' ', $exam->type ?? '')) }}
                            @if($exam->venue) &bull; {{ $exam->venue }} @endif
                            @if($exam->start_time) &bull; {{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }} @endif
                        </small>
                    </div>
                    @if($daysLeft === 0)
                        <span class="badge bg-danger">Today</span>
                    @elseif($daysLeft === 1)
                        <span class="badge bg-warning text-dark">Tomorrow</span>
                    @else
                        <span class="badge bg-light text-dark border">{{ $daysLeft }}d</span>
                    @endif
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-check fs-3 d-block mb-2 opacity-50"></i>
                    No exams in the next 14 days.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- ── RECENT COURSE REGISTRATIONS ────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent py-2 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-pencil-square me-2 text-success"></i>Recent Course Registrations</h6>
                <a href="{{ route('academic.registrations.index') }}" class="btn btn-sm btn-outline-success">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="font-size:0.75rem">Student</th>
                                <th style="font-size:0.75rem">Course</th>
                                <th style="font-size:0.75rem">Status</th>
                                <th style="font-size:0.75rem" class="pe-3">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRegistrations as $reg)
                            @php $rc = ['registered'=>'success','approved'=>'primary','pending'=>'warning','dropped'=>'danger']; @endphp
                            <tr>
                                <td class="ps-3">
                                    <span class="fw-semibold" style="font-size:0.875rem">
                                        {{ optional(optional($reg->student)->user)->name ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ optional(optional($reg->courseOffering)->course)->name ?? '—' }}
                                        <code class="ms-1" style="font-size:0.7rem">{{ optional(optional($reg->courseOffering)->course)->code }}</code>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $rc[$reg->status] ?? 'secondary' }}">
                                        {{ ucfirst($reg->status) }}
                                    </span>
                                </td>
                                <td class="pe-3"><small class="text-muted">{{ $reg->created_at->format('d M') }}</small></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">No registrations this semester/term.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ANNOUNCEMENTS ──────────────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent py-2 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-megaphone me-2 text-secondary"></i>Announcements</h6>
                <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($announcements as $ann)
                <div class="px-3 py-2 border-bottom">
                    <div class="fw-semibold" style="font-size:0.875rem">{{ Str::limit($ann->title, 50) }}</div>
                    <small class="text-muted">{{ $ann->created_at->diffForHumans() }}</small>
                </div>
                @empty
                <div class="text-center text-muted py-4">No announcements.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection
