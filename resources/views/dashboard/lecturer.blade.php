@extends('layouts.app')
@section('title', 'My Dashboard')
@section('page-title', 'Lecturer Dashboard')

@section('content')

{{-- ── WELCOME BANNER ───────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4" style="background:linear-gradient(135deg,var(--primary) 0%,#1a3a6b 100%)">
    <div class="card-body p-4 d-flex align-items-center gap-4">
        <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle border border-3 border-white" style="width:72px;height:72px;object-fit:cover">
        <div class="text-white">
            <h5 class="mb-1 fw-bold">Welcome back, {{ auth()->user()->name }}</h5>
            <div class="opacity-75 small">
                {{ optional($staff?->department)->name ?? 'Faculty' }}
                &bull; {{ optional($staff)->designation ?? 'Lecturer' }}
                @if($currentSemester)
                    &bull; {{ $currentSemester->name }}
                @endif
            </div>
        </div>
        <div class="ms-auto text-white text-end d-none d-md-block">
            <div class="fs-5 fw-bold">{{ now()->format('l') }}</div>
            <div class="opacity-75 small">{{ now()->format('d F Y') }}</div>
        </div>
    </div>
</div>

{{-- ── STAT CARDS ───────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-journal-bookmark-fill"></i></div>
                    <div>
                        <div class="stat-value text-primary">{{ $totalCourses }}</div>
                        <div class="stat-label text-muted">My Courses</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="stat-value text-success">{{ $totalStudents }}</div>
                        <div class="stat-label text-muted">My Students</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-fill"></i></div>
                    <div>
                        <div class="stat-value text-warning">{{ $todayClasses->count() }}</div>
                        <div class="stat-label text-muted">Classes Today</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-file-earmark-text-fill"></i></div>
                    <div>
                        <div class="stat-value text-danger">{{ $upcomingExams->count() }}</div>
                        <div class="stat-label text-muted">Upcoming Exams</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── ALERTS ───────────────────────────────────────────────────────────────── --}}
@if($pendingResults->count() > 0)
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
    <div>
        <strong>Results Pending:</strong>
        {{ $pendingResults->count() }} course{{ $pendingResults->count() != 1 ? 's' : '' }} have enrolled students with no final results submitted yet —
        {{ $pendingResults->pluck('course.code')->implode(', ') }}.
        <a href="{{ route('academic.results.index') }}" class="alert-link ms-1">Submit Results &rarr;</a>
    </div>
</div>
@endif

@if($todayClasses->count() > 0)
<div class="alert alert-info d-flex align-items-center gap-2 mb-4">
    <i class="bi bi-calendar-check-fill fs-5"></i>
    <div>
        You have <strong>{{ $todayClasses->count() }} class{{ $todayClasses->count() != 1 ? 'es' : '' }}</strong> scheduled today.
        <a href="{{ route('academic.timetable.index') }}" class="alert-link ms-1">View Timetable &rarr;</a>
    </div>
</div>
@endif

<div class="row g-4">

    {{-- ── TODAY'S CLASSES ──────────────────────────────────────────────────── --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-clock me-2 text-warning"></i>Today's Classes</h6>
                <a href="{{ route('academic.timetable.index') }}" class="btn btn-sm btn-outline-secondary">Full Timetable</a>
            </div>
            <div class="card-body p-0">
                @forelse($todayClasses as $slot)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="text-center" style="min-width:54px">
                        <div class="fw-bold text-primary small">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}</div>
                        <div class="text-muted" style="font-size:.72rem">{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</div>
                    </div>
                    <div class="flex-fill">
                        <div class="fw-semibold small">{{ optional($slot->courseOffering?->course)->title ?? '—' }}</div>
                        <div class="text-muted" style="font-size:.78rem">
                            {{ optional($slot->courseOffering?->course)->code }}
                            @if($slot->room) &bull; {{ $slot->room }} @endif
                            @if($slot->type) &bull; <span class="badge bg-secondary bg-opacity-25 text-secondary">{{ ucfirst($slot->type) }}</span> @endif
                        </div>
                    </div>
                    <a href="{{ route('academic.attendance.index') }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-person-check me-1"></i>Attendance
                    </a>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-calendar-x fs-3 d-block mb-2 opacity-50"></i>
                    No classes scheduled for today
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── QUICK ACTIONS ────────────────────────────────────────────────────── --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-lightning-fill me-2 text-warning"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('academic.attendance.index') }}" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                            <i class="bi bi-person-check-fill fs-4 mb-1"></i>
                            <span class="small">Mark Attendance</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('academic.results.index') }}" class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-3">
                            <i class="bi bi-bar-chart-fill fs-4 mb-1"></i>
                            <span class="small">
                                Submit Results
                                @if($pendingResults->count() > 0)
                                <span class="badge bg-danger ms-1">{{ $pendingResults->count() }}</span>
                                @endif
                            </span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('academic.examinations.index') }}" class="btn btn-outline-danger w-100 d-flex flex-column align-items-center py-3">
                            <i class="bi bi-file-earmark-text-fill fs-4 mb-1"></i>
                            <span class="small">Examinations</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('academic.timetable.index') }}" class="btn btn-outline-warning w-100 d-flex flex-column align-items-center py-3">
                            <i class="bi bi-calendar3 fs-4 mb-1"></i>
                            <span class="small">My Timetable</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('academic.course-offerings.index') }}" class="btn btn-outline-info w-100 d-flex flex-column align-items-center py-3">
                            <i class="bi bi-journal-bookmark fs-4 mb-1"></i>
                            <span class="small">My Offerings</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('academic.registrations.index') }}" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center py-3">
                            <i class="bi bi-people fs-4 mb-1"></i>
                            <span class="small">My Students</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── MY COURSE OFFERINGS ──────────────────────────────────────────────── --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>
                    My Course Offerings
                    @if($currentSemester) <span class="badge bg-secondary fw-normal ms-1 small">{{ $currentSemester->name }}</span> @endif
                </h6>
                <a href="{{ route('academic.course-offerings.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
            <div class="card-body p-0">
                @forelse($myOfferings as $offering)
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                         style="width:42px;height:42px;background:var(--primary);font-size:.78rem">
                        {{ strtoupper(substr(optional($offering->course)->code ?? 'C', 0, 3)) }}
                    </div>
                    <div class="flex-fill">
                        <div class="fw-semibold small">{{ optional($offering->course)->title ?? '—' }}</div>
                        <div class="text-muted" style="font-size:.78rem">
                            {{ optional($offering->course)->code }}
                            @if($offering->venue) &bull; {{ $offering->venue }} @endif
                        </div>
                        <div class="mt-1" style="max-width:200px">
                            <div class="progress" style="height:4px">
                                @php $pct = $offering->max_students > 0 ? min(100, round(($offering->enrolled / $offering->max_students) * 100)) : 0; @endphp
                                <div class="progress-bar {{ $pct >= 90 ? 'bg-danger' : ($pct >= 70 ? 'bg-warning' : 'bg-success') }}" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center d-none d-md-block" style="min-width:80px">
                        <div class="fw-bold text-primary">{{ $offering->enrolled }}</div>
                        <div class="text-muted" style="font-size:.72rem">/ {{ $offering->max_students }} enrolled</div>
                    </div>
                    <div>
                        <span class="badge bg-{{ $offering->status === 'active' ? 'success' : 'secondary' }} bg-opacity-15 text-{{ $offering->status === 'active' ? 'success' : 'secondary' }}">
                            {{ ucfirst($offering->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-journal-x fs-3 d-block mb-2 opacity-50"></i>
                    No course offerings assigned for this semester/term
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── UPCOMING EXAMS ───────────────────────────────────────────────────── --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-file-earmark-text me-2 text-danger"></i>Upcoming Exams <small class="text-muted fw-normal">(next 14 days)</small></h6>
                <a href="{{ route('academic.examinations.index') }}" class="btn btn-sm btn-outline-secondary">All Exams</a>
            </div>
            <div class="card-body p-0">
                @forelse($upcomingExams as $exam)
                @php $daysLeft = today()->diffInDays($exam->exam_date); @endphp
                <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
                    <div class="text-center rounded p-2 {{ $daysLeft <= 2 ? 'bg-danger' : 'bg-primary' }} bg-opacity-10 flex-shrink-0" style="min-width:46px">
                        <div class="fw-bold {{ $daysLeft <= 2 ? 'text-danger' : 'text-primary' }}" style="font-size:.95rem">{{ $exam->exam_date->format('d') }}</div>
                        <div class="{{ $daysLeft <= 2 ? 'text-danger' : 'text-primary' }}" style="font-size:.7rem">{{ $exam->exam_date->format('M') }}</div>
                    </div>
                    <div class="flex-fill">
                        <div class="fw-semibold small">{{ $exam->name }}</div>
                        <div class="text-muted" style="font-size:.78rem">
                            {{ optional($exam->courseOffering?->course)->code }}
                            @if($exam->venue) &bull; {{ $exam->venue }} @endif
                            @if($exam->start_time) &bull; {{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }} @endif
                        </div>
                    </div>
                    <span class="badge {{ $daysLeft <= 2 ? 'bg-danger' : 'bg-secondary' }} bg-opacity-15 {{ $daysLeft <= 2 ? 'text-danger' : 'text-secondary' }}">
                        {{ $daysLeft === 0 ? 'Today' : "In {$daysLeft}d" }}
                    </span>
                </div>
                @empty
                <div class="text-center text-muted py-5">
                    <i class="bi bi-check-circle text-success fs-3 d-block mb-2 opacity-50"></i>
                    No exams in the next 14 days
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── RECENT ATTENDANCE + ANNOUNCEMENTS ───────────────────────────────── --}}
    <div class="col-lg-6">
        {{-- Recent Attendance Sessions --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-person-check me-2 text-success"></i>Recent Attendance</h6>
                <span class="badge bg-success bg-opacity-15 text-success">{{ $attendanceThisMonth }} this month</span>
            </div>
            <div class="card-body p-0">
                @forelse($recentSessions as $session)
                <div class="d-flex align-items-center gap-3 px-4 py-2 border-bottom">
                    <div class="text-muted" style="font-size:.78rem;min-width:68px">{{ $session->date->format('d M') }}</div>
                    <div class="flex-fill small">
                        <span class="fw-semibold">{{ optional($session->courseOffering?->course)->code ?? '—' }}</span>
                        @if($session->topic) <span class="text-muted"> &bull; {{ Str::limit($session->topic, 30) }}</span> @endif
                    </div>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary small">{{ ucfirst($session->session_type ?? 'class') }}</span>
                </div>
                @empty
                <div class="text-center text-muted py-3 small">No attendance sessions yet</div>
                @endforelse
            </div>
            <div class="card-footer bg-transparent text-end py-2">
                <a href="{{ route('academic.attendance.index') }}" class="btn btn-sm btn-outline-success">Manage Attendance</a>
            </div>
        </div>

        {{-- Announcements --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-megaphone me-2 text-secondary"></i>Announcements</h6>
            </div>
            <div class="card-body p-0">
                @forelse($announcements as $ann)
                <div class="px-4 py-3 border-bottom">
                    <div class="small fw-semibold">{{ Str::limit($ann->title, 50) }}</div>
                    <div class="text-muted" style="font-size:.78rem">{{ $ann->created_at->diffForHumans() }}</div>
                </div>
                @empty
                <div class="text-center text-muted py-3 small">No announcements</div>
                @endforelse
            </div>
            <div class="card-footer bg-transparent text-end py-2">
                <a href="{{ route('announcements.index') }}" class="btn btn-sm btn-outline-secondary">View All</a>
            </div>
        </div>
    </div>

</div>
@endsection
