@extends('layouts.app')
@section('title', $isSelfView ? 'My Attendance' : 'Student Attendance — ' . $student->full_name)
@section('page-title', $isSelfView ? 'My Attendance' : 'Student Attendance')

@section('content')

{{-- ── HEADER ─────────────────────────────────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1">
            <i class="bi bi-person-check-fill me-2 text-primary"></i>
            {{ $isSelfView ? 'My Attendance' : $student->full_name . ' — Attendance' }}
        </h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            @if(!$isSelfView)
            <li class="breadcrumb-item"><a href="{{ route('academic.attendance.index') }}">Attendance</a></li>
            @endif
            <li class="breadcrumb-item active">{{ $isSelfView ? 'My Attendance' : $student->full_name }}</li>
        </ol></nav>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        {{-- Semester/Term filter --}}
        <form method="GET" class="d-flex align-items-center gap-2">
            <select name="semester_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:180px">
                @foreach($semesters as $s)
                <option value="{{ $s->id }}" {{ $semester?->id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </form>
        @if(!$isSelfView)
        <a href="{{ route('academic.attendance.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
        @endif
    </div>
</div>

{{-- ── STAT CARDS ──────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-calendar3"></i></div>
                <div>
                    <div class="stat-value text-primary">{{ $totalSessions }}</div>
                    <div class="stat-label text-muted">Total Sessions</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill"></i></div>
                <div>
                    <div class="stat-value text-success">{{ $totalPresent }}</div>
                    <div class="stat-label text-muted">Present</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-x-circle-fill"></i></div>
                <div>
                    <div class="stat-value text-danger">{{ $totalAbsent }}</div>
                    <div class="stat-label text-muted">
                        Absent
                        @if($totalLate > 0)
                            <span class="text-warning fw-normal">/ {{ $totalLate }} late</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                @php $rateColor = $overallRate === null ? 'secondary' : ($overallRate >= 75 ? 'success' : ($overallRate >= 60 ? 'warning' : 'danger')); @endphp
                <div class="stat-icon bg-{{ $rateColor }} bg-opacity-10 text-{{ $rateColor }}"><i class="bi bi-graph-up"></i></div>
                <div>
                    <div class="stat-value text-{{ $rateColor }}">
                        {{ $overallRate !== null ? $overallRate . '%' : '—' }}
                    </div>
                    <div class="stat-label text-muted">Overall Rate</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── OVERALL PROGRESS BAR ────────────────────────────────────────────────── --}}
@if($totalSessions > 0)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-semibold">Overall Attendance — {{ $semester?->name }}</span>
            <span class="fw-bold text-{{ $rateColor }}">{{ $overallRate }}%</span>
        </div>
        <div class="progress" style="height:12px;border-radius:8px">
            <div class="progress-bar bg-success" style="width:{{ ($totalSessions > 0 ? ($totalPresent/$totalSessions)*100 : 0) }}%"
                 title="Present: {{ $totalPresent }}"></div>
            <div class="progress-bar bg-warning" style="width:{{ ($totalSessions > 0 ? ($totalLate/$totalSessions)*100 : 0) }}%"
                 title="Late: {{ $totalLate }}"></div>
            <div class="progress-bar bg-danger" style="width:{{ ($totalSessions > 0 ? ($totalAbsent/$totalSessions)*100 : 0) }}%"
                 title="Absent: {{ $totalAbsent }}"></div>
        </div>
        <div class="d-flex gap-3 mt-2" style="font-size:0.78rem">
            <span><span class="badge bg-success">{{ $totalPresent }}</span> Present</span>
            @if($totalLate > 0)<span><span class="badge bg-warning text-dark">{{ $totalLate }}</span> Late</span>@endif
            <span><span class="badge bg-danger">{{ $totalAbsent }}</span> Absent</span>
        </div>
        @if($overallRate !== null && $overallRate < 75)
        <div class="alert alert-warning mt-3 mb-0 py-2 px-3" style="font-size:0.83rem">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Your overall attendance is <strong>{{ $overallRate }}%</strong>, below the required <strong>75%</strong>.
            Please attend more classes to avoid academic penalties.
        </div>
        @endif
    </div>
</div>
@endif

{{-- ── PER-COURSE BREAKDOWN ────────────────────────────────────────────────── --}}
@if($courseStats->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
        No course registrations found for {{ $semester?->name ?? 'this semester/term' }}.
    </div>
</div>
@else
<div class="row g-3">
    @foreach($courseStats as $cs)
    @php
        $r = $cs['rate'];
        $rColor = $r === null ? 'secondary' : ($r >= 75 ? 'success' : ($r >= 60 ? 'warning' : 'danger'));
        $cardId = 'course-' . $cs['offering']->id;
    @endphp
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            {{-- Course header --}}
            <div class="card-header bg-transparent d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                         style="width:44px;height:44px;background:rgba(11,31,58,0.08);color:#0B1F3A;font-size:0.75rem">
                        {{ strtoupper(substr($cs['offering']->course?->code ?? 'C', 0, 4)) }}
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $cs['offering']->course?->name }}</div>
                        <small class="text-muted">
                            {{ $cs['offering']->course?->code }}
                            @if($cs['offering']->course?->credit_hours)
                                &bull; {{ $cs['offering']->course->credit_hours }} credit hrs
                            @endif
                        </small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 flex-shrink-0">
                    @if($r !== null)
                    <div class="text-end d-none d-md-block" style="min-width:120px">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:8px;border-radius:6px">
                                <div class="progress-bar bg-{{ $rColor }}" style="width:{{ $r }}%"></div>
                            </div>
                            <span class="fw-bold text-{{ $rColor }}" style="min-width:38px">{{ $r }}%</span>
                        </div>
                        <small class="text-muted">{{ $cs['present'] }}/{{ $cs['total'] }} sessions</small>
                    </div>
                    @endif
                    @if($r !== null && $r < 75)
                    <span class="badge bg-danger">Low</span>
                    @elseif($r !== null && $r >= 75)
                    <span class="badge bg-success">On Track</span>
                    @else
                    <span class="badge bg-secondary">No Data</span>
                    @endif
                    @if($cs['total'] > 0)
                    <button class="btn btn-sm btn-outline-secondary" type="button"
                            data-bs-toggle="collapse" data-bs-target="#{{ $cardId }}"
                            aria-expanded="false">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    @endif
                </div>
            </div>

            {{-- Mobile rate bar --}}
            @if($r !== null)
            <div class="d-md-none px-3 pb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="progress flex-grow-1" style="height:6px">
                        <div class="progress-bar bg-{{ $rColor }}" style="width:{{ $r }}%"></div>
                    </div>
                    <small class="fw-bold text-{{ $rColor }}">{{ $r }}%</small>
                </div>
                <small class="text-muted">{{ $cs['present'] }}/{{ $cs['total'] }} sessions attended</small>
            </div>
            @endif

            {{-- Stats strip --}}
            @if($cs['total'] > 0)
            <div class="px-3 pb-2 d-flex gap-3" style="font-size:0.8rem">
                <span class="text-success"><i class="bi bi-check-circle me-1"></i>{{ $cs['present'] }} present</span>
                <span class="text-danger"><i class="bi bi-x-circle me-1"></i>{{ $cs['absent'] }} absent</span>
                @if($cs['late'] > 0)
                <span class="text-warning"><i class="bi bi-clock me-1"></i>{{ $cs['late'] }} late</span>
                @endif
            </div>
            @endif

            {{-- Session detail (collapsible) --}}
            @if($cs['total'] > 0)
            <div class="collapse" id="{{ $cardId }}">
                <div class="card-body p-0 border-top">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Date</th>
                                    <th>Type</th>
                                    <th>Topic</th>
                                    <th class="text-center">Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cs['offering']->attendanceSessions as $session)
                                @php
                                    $record   = $session->records->first();
                                    $status   = $record?->status ?? null;
                                    $sBadge   = match($status) {
                                        'present' => 'success',
                                        'absent'  => 'danger',
                                        'late'    => 'warning text-dark',
                                        default   => 'light text-muted',
                                    };
                                    $typeColors = ['lecture'=>'primary','tutorial'=>'info','lab'=>'warning','seminar'=>'secondary'];
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <span class="fw-semibold">{{ \Carbon\Carbon::parse($session->date)->format('d M Y') }}</span>
                                        <small class="d-block text-muted">{{ \Carbon\Carbon::parse($session->date)->format('l') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $typeColors[$session->session_type] ?? 'secondary' }}">
                                            {{ ucfirst($session->session_type ?? 'Lecture') }}
                                        </span>
                                    </td>
                                    <td><small>{{ $session->topic ?? '—' }}</small></td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $sBadge }}">
                                            {{ $status ? ucfirst($status) : '—' }}
                                        </span>
                                    </td>
                                    <td><small class="text-muted">{{ $record?->remarks ?? '—' }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
