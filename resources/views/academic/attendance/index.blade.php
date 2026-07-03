@extends('layouts.app')
@section('title', 'Attendance Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Attendance Management</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Attendance</li>
        </ol></nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#takeProgramModal">
        <i class="bi bi-plus-circle me-1"></i> Take Attendance
    </button>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h4 class="fw-bold text-primary">{{ $stats['today_sessions'] }}</h4>
                <small class="text-muted">Sessions Today</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h4 class="fw-bold text-success">{{ $stats['today_present'] }}</h4>
                <small class="text-muted">Present Today</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h4 class="fw-bold text-danger">{{ $stats['today_absent'] }}</h4>
                <small class="text-muted">Absent Today</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <h4 class="fw-bold text-info">{{ $stats['week_sessions'] }}</h4>
                <small class="text-muted">Sessions This Week</small>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="program_id" class="form-select form-select-sm">
                    <option value="">All Programs</option>
                    @foreach($programs as $prog)
                    <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                        {{ $prog->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="From">
            </div>
            <div class="col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="To">
            </div>
            <div class="col-md-2">
                <button class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('academic.attendance.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Sessions Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Program</th>
                        <th>Course</th>
                        <th>Session Type</th>
                        <th>Topic</th>
                        <th class="text-center">Present</th>
                        <th class="text-center">Absent</th>
                        <th class="text-center">Late</th>
                        <th class="text-center">Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                    @php
                        $rate = $session->total_count > 0
                            ? round(($session->present_count / $session->total_count) * 100)
                            : 0;
                    @endphp
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($session->date)->format('d M Y') }}</span><br>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($session->date)->format('l') }}</small>
                        </td>
                        <td>
                            @if($session->program_name)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    {{ $session->program_code ?? $session->program_name }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($session->course_code)
                                <code>{{ $session->course_code }}</code>
                                <small class="d-block text-muted">{{ $session->course_name }}</small>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @php $typeColors = ['lecture'=>'primary','tutorial'=>'info','lab'=>'warning','seminar'=>'secondary'] @endphp
                            <span class="badge bg-{{ $typeColors[$session->session_type] ?? 'secondary' }}">
                                {{ ucfirst($session->session_type ?? 'lecture') }}
                            </span>
                        </td>
                        <td>{{ $session->topic ?? '—' }}</td>
                        <td class="text-center"><span class="badge bg-success">{{ $session->present_count }}</span></td>
                        <td class="text-center"><span class="badge bg-danger">{{ $session->absent_count }}</span></td>
                        <td class="text-center"><span class="badge bg-warning text-dark">{{ $session->late_count }}</span></td>
                        <td class="text-center" style="min-width:100px">
                            <div class="d-flex align-items-center gap-1">
                                <div class="progress flex-grow-1" style="height:6px">
                                    <div class="progress-bar bg-{{ $rate >= 75 ? 'success' : ($rate >= 60 ? 'warning' : 'danger') }}"
                                         style="width:{{ $rate }}%"></div>
                                </div>
                                <small class="text-muted">{{ $rate }}%</small>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                            No attendance sessions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($sessions->hasPages())
    <div class="card-footer bg-transparent">
        {{ $sessions->links() }}
    </div>
    @endif
</div>

{{-- Take Attendance Modal --}}
<div class="modal fade" id="takeProgramModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-clipboard-check me-2"></i>Take Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form onsubmit="startByProgram(event)">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Program *</label>
                        <select class="form-select" id="programSelect" required onchange="loadOfferings(this.value)">
                            <option value="">— Select Program —</option>
                            @foreach($programs as $prog)
                            <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Course *</label>
                        <select class="form-select" id="offeringSelect" required disabled>
                            <option value="">— Select Program First —</option>
                        </select>
                        <div class="form-text" id="offeringHint"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-play-circle me-1"></i> Continue</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loadOfferings(programId) {
    const sel  = document.getElementById('offeringSelect');
    const hint = document.getElementById('offeringHint');

    sel.innerHTML    = '<option value="">Loading...</option>';
    sel.disabled     = true;
    hint.textContent = '';

    if (!programId) {
        sel.innerHTML = '<option value="">— Select Program First —</option>';
        return;
    }

    fetch('/ajax/offerings/by-program/' + programId)
        .then(r => r.json())
        .then(data => {
            sel.innerHTML = '<option value="">— Select Course —</option>';
            if (data.length === 0) {
                hint.textContent = 'No courses with active offerings found for this program in the current semester/term.';
            }
            data.forEach(c => {
                const opt   = document.createElement('option');
                opt.value   = c.offering_id;   // offering_id used for navigation
                opt.textContent = c.code + ' — ' + c.name;
                sel.appendChild(opt);
            });
            sel.disabled = false;
        })
        .catch(() => {
            sel.innerHTML = '<option value="">Failed to load courses</option>';
        });
}

function startByProgram(e) {
    e.preventDefault();
    const programId  = document.getElementById('programSelect').value;
    const offeringId = document.getElementById('offeringSelect').value;
    if (!programId)  { alert('Please select a program.'); return; }
    if (!offeringId) { alert('Please select a course.'); return; }
    window.location = '/academic/attendance/by-program/' + programId + '/' + offeringId;
}
</script>
@endpush
