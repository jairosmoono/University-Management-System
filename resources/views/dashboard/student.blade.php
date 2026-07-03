@extends('layouts.app')
@section('title', 'My Dashboard')
@section('page-title', 'Student Dashboard')

@section('content')

{{-- ── WELCOME BANNER ──────────────────────────────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4" style="background:linear-gradient(135deg,var(--primary) 0%,#1a3a6b 100%)">
    <div class="card-body p-4 d-flex align-items-center gap-4">
        <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle border border-3 border-white"
             style="width:72px;height:72px;object-fit:cover" alt="">
        <div class="text-white">
            <h5 class="mb-1 fw-bold">Welcome back, {{ auth()->user()->name }}</h5>
            <div class="opacity-75 small">
                {{ $student?->student_id ?? '—' }}
                &bull; {{ optional($student?->program)->name ?? 'Student' }}
                @if($student?->program?->department)
                    &bull; {{ $student->program->department->name }}
                @endif
            </div>
        </div>
        <div class="ms-auto text-white text-end d-none d-md-block">
            @if($currentSemester)
            <div class="fs-5 fw-bold">{{ $currentSemester->name }}</div>
            @endif
            <div class="opacity-75 small">{{ now()->format('d F Y') }}</div>
            @if($currentAcademicYear)
            <div class="opacity-75 small">{{ $currentAcademicYear->name }}</div>
            @endif
        </div>
    </div>
</div>

{{-- ── STAT CARDS ───────────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-journal-bookmark-fill"></i></div>
                    <div>
                        <div class="stat-value text-primary">{{ $myCourses->count() }}</div>
                        <div class="stat-label text-muted">Registered Courses</div>
                        <div class="small text-muted mt-1">This semester/term</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-award-fill"></i></div>
                    <div>
                        <div class="stat-value text-success">
                            {{ $latestGpaRecord ? number_format($latestGpaRecord->gpa, 2) : '—' }}
                        </div>
                        <div class="stat-label text-muted">Semester/Term GPA</div>
                        <div class="small text-muted mt-1">
                            CGPA: {{ $latestGpaRecord ? number_format($latestGpaRecord->cgpa, 2) : '—' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-person-check-fill"></i></div>
                    <div>
                        <div class="stat-value {{ $attendanceRate !== null ? ($attendanceRate >= 75 ? 'text-success' : 'text-danger') : 'text-muted' }}">
                            {{ $attendanceRate !== null ? $attendanceRate . '%' : '—' }}
                        </div>
                        <div class="stat-label text-muted">Attendance Rate</div>
                        <div class="small text-muted mt-1">{{ $presentCount }}/{{ $totalSessions }} sessions</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-receipt-cutoff"></i></div>
                    <div>
                        <div class="stat-value {{ $outstandingBalance > 0 ? 'text-danger' : 'text-success' }}">
                            {{ formatCurrency($outstandingBalance) }}
                        </div>
                        <div class="stat-label text-muted">Outstanding Balance</div>
                        <div class="small mt-1">
                            @if($outstandingBalance > 0)
                                <a href="{{ route('finance.billing.index') }}" class="text-danger fw-semibold">Pay now</a>
                            @else
                                <span class="text-success">No balance due</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── MY COURSES + QUICK ACTIONS ───────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-journal-bookmark-fill me-2 text-primary"></i>My Courses This Semester/Term
                </h6>
                <a href="{{ route('academic.registrations.index') }}" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body p-0">
                @forelse($myCourses as $offering)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <div class="rounded d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:40px;height:40px;background:rgba(11,31,58,0.08);color:#0B1F3A;font-size:0.75rem;font-weight:700">
                        {{ strtoupper(substr($offering->course?->code ?? 'C', 0, 3)) }}
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="fw-semibold text-truncate" style="font-size:0.875rem">
                            {{ $offering->course?->name }}
                        </div>
                        <div class="text-muted" style="font-size:0.75rem">
                            {{ $offering->course?->code }}
                            @if($offering->course?->credit_hours)
                                &bull; {{ $offering->course->credit_hours }} credit hrs
                            @endif
                            @if($offering->lecturer?->user)
                                &bull; {{ $offering->lecturer->user->name }}
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted p-4">
                    <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                    No courses registered for this semester/term.
                    <div class="mt-2">
                        <a href="{{ route('academic.course-offerings.index') }}" class="btn btn-sm btn-outline-primary">Browse Offerings</a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-lightning-fill me-2 text-warning"></i>Quick Actions
                </h6>
            </div>
            <div class="card-body p-3">
                <a href="{{ route('academic.course-offerings.index') }}"
                   class="btn btn-outline-primary w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-pencil-square"></i> Register for Courses
                </a>
                <a href="{{ route('academic.timetable.index') }}"
                   class="btn btn-outline-secondary w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-clock"></i> View My Timetable
                </a>
                <a href="{{ route('academic.results.index') }}"
                   class="btn btn-outline-success w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart"></i> View My Results
                </a>
                <a href="{{ route('finance.billing.index') }}"
                   class="btn btn-outline-danger w-100 text-start mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-receipt"></i> My Fees &amp; Bills
                </a>
                <a href="{{ route('support.create') }}"
                   class="btn btn-outline-warning w-100 text-start d-flex align-items-center gap-2">
                    <i class="bi bi-headset"></i> Get Support
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── UPCOMING EXAMS + RECENT RESULTS ─────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-calendar-event me-2 text-danger"></i>Upcoming Examinations
                </h6>
                <a href="{{ route('academic.examinations.index') }}" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body p-0">
                @forelse($upcomingExams as $exam)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <div class="text-center flex-shrink-0"
                         style="min-width:46px">
                        <div class="fw-bold text-danger" style="font-size:1.1rem;line-height:1">
                            {{ \Carbon\Carbon::parse($exam->exam_date)->format('d') }}
                        </div>
                        <div class="text-muted" style="font-size:0.7rem;text-transform:uppercase">
                            {{ \Carbon\Carbon::parse($exam->exam_date)->format('M') }}
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:0.85rem">{{ $exam->name }}</div>
                        <div class="text-muted" style="font-size:0.75rem">
                            {{ $exam->courseOffering?->course?->code }}
                            &bull; {{ \Carbon\Carbon::parse($exam->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($exam->end_time)->format('H:i') }}
                            @if($exam->venue) &bull; {{ $exam->venue }} @endif
                        </div>
                    </div>
                    <span class="badge bg-{{ \Carbon\Carbon::parse($exam->exam_date)->isToday() ? 'danger' : (\Carbon\Carbon::parse($exam->exam_date)->isTomorrow() ? 'warning text-dark' : 'light text-dark') }}">
                        {{ \Carbon\Carbon::parse($exam->exam_date)->isToday() ? 'Today' : (\Carbon\Carbon::parse($exam->exam_date)->isTomorrow() ? 'Tomorrow' : \Carbon\Carbon::parse($exam->exam_date)->diffForHumans()) }}
                    </span>
                </div>
                @empty
                <div class="text-center text-muted p-4">
                    <i class="bi bi-calendar-check fs-3 d-block mb-2"></i>
                    No exams in the next 14 days.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-bar-chart-fill me-2 text-success"></i>Recent Results
                </h6>
                <a href="{{ route('academic.results.index') }}" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body p-0">
                @forelse($recentResults as $result)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <div class="text-center flex-shrink-0" style="min-width:46px">
                        @php
                            $gradeColor = match(true) {
                                in_array($result->grade, ['A+','A','A-']) => 'success',
                                in_array($result->grade, ['B+','B','B-']) => 'primary',
                                in_array($result->grade, ['C+','C','C-']) => 'warning',
                                default => 'danger',
                            };
                        @endphp
                        <span class="badge bg-{{ $gradeColor }} fs-6 px-2">{{ $result->grade ?? '—' }}</span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:0.85rem">
                            {{ $result->courseOffering?->course?->name ?? 'Course' }}
                        </div>
                        <div class="text-muted" style="font-size:0.75rem">
                            {{ $result->courseOffering?->course?->code }}
                            &bull; {{ $result->courseOffering?->semester?->name ?? $result->created_at->format('M Y') }}
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-semibold" style="font-size:0.85rem">{{ $result->total_score ?? '—' }}</div>
                        <span class="badge bg-{{ $result->status === 'pass' ? 'success' : ($result->status === 'fail' ? 'danger' : 'secondary') }}" style="font-size:0.65rem">
                            {{ ucfirst($result->status ?? 'pending') }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted p-4">
                    <i class="bi bi-bar-chart fs-3 d-block mb-2"></i>
                    No results available yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ── ATTENDANCE OVERVIEW + ANNOUNCEMENTS ─────────────────────────────────────── --}}
<div class="row g-3">
    @if($totalSessions > 0)
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-person-check-fill me-2 text-warning"></i>Attendance Overview
                </h6>
                <a href="{{ route('academic.attendance.index') }}" class="btn btn-link btn-sm p-0">Details</a>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <div class="fs-2 fw-bold {{ $attendanceRate >= 75 ? 'text-success' : 'text-danger' }}">
                            {{ $attendanceRate }}%
                        </div>
                        <div class="text-muted small">Overall attendance rate</div>
                    </div>
                    <div class="text-end">
                        <div class="text-success fw-semibold">{{ $presentCount }} present</div>
                        <div class="text-danger">{{ $totalSessions - $presentCount }} absent</div>
                        <div class="text-muted small">of {{ $totalSessions }} sessions</div>
                    </div>
                </div>
                <div class="progress" style="height:10px;border-radius:8px">
                    <div class="progress-bar bg-{{ $attendanceRate >= 75 ? 'success' : 'danger' }}"
                         style="width:{{ $attendanceRate }}%"></div>
                </div>
                @if($attendanceRate < 75)
                <div class="alert alert-warning alert-sm mt-3 mb-0 py-2 px-3" style="font-size:0.8rem">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Your attendance is below the 75% minimum requirement.
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="col-lg-{{ $totalSessions > 0 ? '6' : '12' }}">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header d-flex align-items-center justify-content-between py-3 border-bottom">
                <h6 class="card-title mb-0 fw-semibold">
                    <i class="bi bi-megaphone-fill me-2 text-info"></i>Recent Announcements
                </h6>
                <a href="{{ route('announcements.index') }}" class="btn btn-link btn-sm p-0">View all</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-bottom">
                    @forelse($announcements as $ann)
                    <a href="{{ route('announcements.show', $ann) }}"
                       class="list-group-item list-group-item-action px-3 py-2">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-{{ ['general'=>'secondary','academic'=>'primary','finance'=>'success','events'=>'info','emergency'=>'danger'][$ann->category] ?? 'secondary' }} mt-1"
                                  style="font-size:0.65rem">
                                {{ ucfirst($ann->category) }}
                            </span>
                            <div class="flex-1">
                                <div class="fw-semibold" style="font-size:0.85rem">{{ $ann->title }}</div>
                                <div class="text-muted" style="font-size:0.75rem">{{ $ann->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="text-center text-muted p-4">No announcements</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
